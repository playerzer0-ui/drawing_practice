import asyncio
import json
import os
import sys
from playwright.async_api import async_playwright

if len(sys.argv) != 3:
    print(json.dumps({"error": "Expected 2 arguments: query and cache_name"}))
    sys.exit(1)

QUERY = sys.argv[1]        # e.g. "objects"
CACHE_FILE = f"{sys.argv[2]}.json"

def save_cache(images):
    with open(CACHE_FILE, "w", encoding="utf-8") as f:
        json.dump(images, f)

def load_cache():
    if os.path.exists(CACHE_FILE):
        with open(CACHE_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    return None

async def get_pinterest_images():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()

        await page.goto(
            QUERY,
            timeout=30000
        )

        await page.wait_for_timeout(5000)
        await page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        await page.wait_for_timeout(2000)

        images = await page.evaluate("""
            () => {
                const results = [];

                document.querySelectorAll('a[href^="/pin/"]').forEach(a => {
                    const href = a.getAttribute('href');
                    const match = href.match(/\\/pin\\/(\\d+)\\//);
                    if (!match) return;

                    const img = a.querySelector('img');
                    if (!img) return;

                    const src = img.src || img.getAttribute('data-src');
                    if (!src || !src.includes('pinimg.com')) return;

                    results.push({
                        id: match[1],
                        url: src
                    });
                });

                return results;
            }
        """)

        await browser.close()
        return images

# ---- ENTRY POINT ----
if __name__ == "__main__":
    cached = load_cache()
    if cached:
        print(json.dumps(cached))
        exit()

    images = asyncio.run(get_pinterest_images())
    save_cache(images)
    print(json.dumps(images))

