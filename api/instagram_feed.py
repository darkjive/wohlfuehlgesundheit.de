#!/usr/bin/env python3
"""
Instagram Feed Scraper for wohlfuehlgesundheit.de
Uses reelscraper to fetch Instagram posts and generate static JSON feed
Python 3.13.7 compatible
"""

import json
import os
import sys
from datetime import datetime
from pathlib import Path
from typing import List, Dict, Any, Optional

try:
    from reelscraper import InstagramScraper
except ImportError:
    print("Error: reelscraper not installed. Install with: pip install reelscraper", file=sys.stderr)
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
        self.scraper = InstagramScraper()

    def fetch_posts(self) -> List[Dict[str, Any]]:
        """
        Fetch Instagram posts from profile

        Returns:
            List of post dictionaries
        """
        try:
            # Fetch profile posts
            profile_data = self.scraper.get_profile(self.username)
            posts = []

            if not profile_data or 'edge_owner_to_timeline_media' not in profile_data:
                print(f"Warning: No posts found for @{self.username}", file=sys.stderr)
                return []

            edges = profile_data['edge_owner_to_timeline_media']['edges'][:self.max_posts]

            for edge in edges:
                node = edge.get('node', {})

                # Extract media URL
                media_url = node.get('display_url', '')
                thumbnail_url = node.get('thumbnail_src', media_url)

                # Determine media type
                media_type = 'IMAGE'
                if node.get('is_video'):
                    media_type = 'VIDEO'
                elif node.get('edge_sidecar_to_children'):
                    media_type = 'CAROUSEL_ALBUM'

                # Extract caption
                caption_edges = node.get('edge_media_to_caption', {}).get('edges', [])
                caption = caption_edges[0]['node']['text'] if caption_edges else ''

                # Build post object
                post = {
                    'id': node.get('id', ''),
                    'caption': caption,
                    'mediaUrl': media_url,
                    'mediaType': media_type,
                    'permalink': f"https://www.instagram.com/p/{node.get('shortcode', '')}/",
                    'timestamp': datetime.fromtimestamp(
                        node.get('taken_at_timestamp', 0)
                    ).isoformat(),
                    'thumbnailUrl': thumbnail_url,
                    'likesCount': node.get('edge_liked_by', {}).get('count', 0),
                    'commentsCount': node.get('edge_media_to_comment', {}).get('count', 0)
                }

                posts.append(post)

            return posts

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
