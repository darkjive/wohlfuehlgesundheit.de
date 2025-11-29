#!/usr/bin/env python3
"""
Instagram Feed Scraper for wohlfuehlgesundheit.de
Uses instaloader to fetch Instagram posts and generate static JSON feed
Downloads images locally to avoid CORS issues
Python 3.7+ compatible
"""

import json
import sys
import urllib.request
from datetime import datetime
from pathlib import Path
from typing import List, Dict, Any, Optional

try:
    import instaloader
except ImportError:
    print("Error: instaloader not installed. Install with: pip install instaloader", file=sys.stderr)
    sys.exit(1)


class InstagramFeedGenerator:
    """Generate JSON feed from Instagram profile with local image storage"""

    def __init__(self, username: str, max_posts: int = 12, download_images: bool = True):
        """
        Initialize Instagram feed generator

        Args:
            username: Instagram username (without @)
            max_posts: Maximum number of posts to fetch
            download_images: Download images locally to avoid CORS (default: True)
        """
        self.username = username
        self.max_posts = max_posts
        self.download_images = download_images
        self.loader = instaloader.Instaloader(
            download_videos=False,
            download_video_thumbnails=False,
            download_geotags=False,
            download_comments=False,
            save_metadata=False,
            compress_json=False,
            quiet=True
        )

        # Image storage directory
        self.images_dir = Path('public/data/instagram')
        if self.download_images:
            self.images_dir.mkdir(parents=True, exist_ok=True)

    def download_image(self, url: str, filename: str) -> str:
        """
        Download image from URL to local storage

        Args:
            url: Image URL
            filename: Local filename

        Returns:
            Relative path to downloaded image
        """
        try:
            image_path = self.images_dir / filename

            # Skip if already exists
            if image_path.exists():
                return f'/data/instagram/{filename}'

            # Download image
            headers = {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            }
            req = urllib.request.Request(url, headers=headers)

            with urllib.request.urlopen(req) as response:
                image_data = response.read()

            with open(image_path, 'wb') as f:
                f.write(image_data)

            return f'/data/instagram/{filename}'

        except Exception as e:
            print(f"Warning: Could not download image {filename}: {e}", file=sys.stderr)
            # Fallback to original URL
            return url

    def fetch_posts(self) -> List[Dict[str, Any]]:
        """
        Fetch Instagram posts from profile

        Returns:
            List of post dictionaries
        """
        try:
            # Get profile
            profile = instaloader.Profile.from_username(self.loader.context, self.username)
            posts = []

            # Fetch posts
            for index, post in enumerate(profile.get_posts()):
                if index >= self.max_posts:
                    break

                # Determine media type
                media_type = 'IMAGE'
                if post.is_video:
                    media_type = 'VIDEO'
                elif post.typename == 'GraphSidecar':
                    media_type = 'CAROUSEL_ALBUM'

                # Get media URL
                media_url = post.url

                # Download image locally if enabled
                if self.download_images:
                    # Use shortcode as filename (unique)
                    extension = 'jpg'  # Instagram uses jpg/webp
                    filename = f'{post.shortcode}.{extension}'
                    local_media_url = self.download_image(media_url, filename)
                else:
                    local_media_url = media_url

                # Build post object
                post_data = {
                    'id': str(post.mediaid),
                    'caption': post.caption if post.caption else '',
                    'mediaUrl': local_media_url,
                    'mediaType': media_type,
                    'permalink': f"https://www.instagram.com/p/{post.shortcode}/",
                    'timestamp': post.date_utc.isoformat(),
                    'thumbnailUrl': local_media_url,  # Same as mediaUrl for now
                    'likesCount': post.likes,
                    'commentsCount': post.comments
                }

                posts.append(post_data)
                print(f"✓ Fetched post {index + 1}/{self.max_posts}: {post.shortcode}")

            if not posts:
                print(f"Warning: No posts found for @{self.username}", file=sys.stderr)

            return posts

        except instaloader.exceptions.ProfileNotExistsException:
            print(f"Error: Instagram profile '@{self.username}' does not exist", file=sys.stderr)
            return []
        except instaloader.exceptions.ConnectionException as e:
            print(f"Error: Network connection failed: {e}", file=sys.stderr)
            return []
        except Exception as e:
            print(f"Error fetching Instagram posts: {e}", file=sys.stderr)
            return []

    def generate_feed(self, output_path: Optional[str] = None) -> str:
        """
        Generate JSON feed file

        Args:
            output_path: Path to output JSON file (default: public/data/instagram-feed.json)

        Returns:
            Path to generated JSON file
        """
        if output_path is None:
            output_path = 'public/data/instagram-feed.json'

        # Ensure directory exists
        output_file = Path(output_path)
        output_file.parent.mkdir(parents=True, exist_ok=True)

        # Fetch posts
        posts = self.fetch_posts()

        # Build feed data
        feed_data = {
            'username': self.username,
            'posts': posts,
            'fetchedAt': datetime.now().isoformat(),
            'postsCount': len(posts)
        }

        # Write JSON file
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(feed_data, f, ensure_ascii=False, indent=2)

        print(f"✓ Generated Instagram feed: {output_file} ({len(posts)} posts)")
        return str(output_file)


def main():
    """Main entry point for CLI usage"""
    import argparse

    parser = argparse.ArgumentParser(description='Generate Instagram feed JSON')
    parser.add_argument(
        'username',
        nargs='?',
        default='wohl_fuehl_gesundheit',
        help='Instagram username (default: wohl_fuehl_gesundheit)'
    )
    parser.add_argument(
        '--max-posts',
        type=int,
        default=12,
        help='Maximum number of posts to fetch (default: 12)'
    )
    parser.add_argument(
        '--output',
        '-o',
        default='public/data/instagram-feed.json',
        help='Output JSON file path (default: public/data/instagram-feed.json)'
    )
    parser.add_argument(
        '--no-download',
        action='store_true',
        help='Do not download images locally (may cause CORS issues)'
    )

    args = parser.parse_args()

    # Generate feed
    generator = InstagramFeedGenerator(
        username=args.username,
        max_posts=args.max_posts,
        download_images=not args.no_download
    )
    generator.generate_feed(output_path=args.output)


if __name__ == '__main__':
    main()
