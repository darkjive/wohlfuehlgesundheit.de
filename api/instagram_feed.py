#!/usr/bin/env python3
"""
Instagram Feed Scraper for wohlfuehlgesundheit.de
Uses instaloader to fetch Instagram posts and generate static JSON feed
Python 3.7+ compatible
"""

import json
import sys
from datetime import datetime
from pathlib import Path
from typing import List, Dict, Any, Optional

try:
    import instaloader
except ImportError:
    print("Error: instaloader not installed. Install with: pip install instaloader", file=sys.stderr)
    sys.exit(1)


class InstagramFeedGenerator:
    """Generate JSON feed from Instagram profile"""

    def __init__(self, username: str, max_posts: int = 12):
        """
        Initialize Instagram feed generator

        Args:
            username: Instagram username (without @)
            max_posts: Maximum number of posts to fetch
        """
        self.username = username
        self.max_posts = max_posts
        self.loader = instaloader.Instaloader(
            download_videos=False,
            download_video_thumbnails=False,
            download_geotags=False,
            download_comments=False,
            save_metadata=False,
            compress_json=False,
            quiet=True
        )

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
                thumbnail_url = post.url

                # Build post object
                post_data = {
                    'id': str(post.mediaid),
                    'caption': post.caption if post.caption else '',
                    'mediaUrl': media_url,
                    'mediaType': media_type,
                    'permalink': f"https://www.instagram.com/p/{post.shortcode}/",
                    'timestamp': post.date_utc.isoformat(),
                    'thumbnailUrl': thumbnail_url,
                    'likesCount': post.likes,
                    'commentsCount': post.comments
                }

                posts.append(post_data)

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

    args = parser.parse_args()

    # Generate feed
    generator = InstagramFeedGenerator(
        username=args.username,
        max_posts=args.max_posts
    )
    generator.generate_feed(output_path=args.output)


if __name__ == '__main__':
    main()
