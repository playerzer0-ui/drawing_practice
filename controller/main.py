import asyncio
import json
import os
import sys
import random
from playwright.async_api import async_playwright

if len(sys.argv) != 3:
    print(json.dumps({"error": "Expected 3 arguments: query and cache_name"}))
    sys.exit(1)

# ---------------- CONFIG ----------------
LINE_TRACE_VARIANTS = [
    "black and white line art",
    "ink drawing",
    "pen sketch",
    "outline illustration",
    "monochrome drawing"
]

OBJECT_VARIANTS = [
    "realistic photo",
    "high detail photography",
    "studio lighting",
    "colorful objects",
    "natural lighting",
    "real life reference"
]

BASE_QUERY = sys.argv[1]

if "objects" in BASE_QUERY.lower():
    VARIANTS = OBJECT_VARIANTS
else:
    VARIANTS = LINE_TRACE_VARIANTS

CACHE_NAME = sys.argv[2]

SCROLL_TIMES = 5        # increase for bigger batch
MAX_RESULTS = 40        # how many to store in cache

# ----------------------------------------

# Randomize query
variant = random.choice(VARIANTS)
QUERY = f"{BASE_QUERY} {variant}"

CACHE_FOLDER = os.path.join(os.path.dirname(__file__), "..", "cache")
os.makedirs(CACHE_FOLDER, exist_ok=True)

CACHE_FILE = os.path.join(CACHE_FOLDER, f"{CACHE_NAME}.json")


def save_cache(images):
    with open(CACHE_FILE, "w", encoding="utf-8") as f:
        json.dump(images, f, ensure_ascii=False)


def load_cache():
    if os.path.exists(CACHE_FILE):
        with open(CACHE_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    return None


async def get_pinterest_images():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()

        await page.goto(QUERY, timeout=30000)
        await page.wait_for_timeout(3000)

        # Scroll multiple times to load more pins
        for _ in range(SCROLL_TIMES):
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
        sys.exit(0)

    images = asyncio.run(get_pinterest_images())

    # Remove duplicates (by Pinterest ID)
    unique = {}
    for img in images:
        unique[img["id"]] = img

    images = list(unique.values())

    # Shuffle for randomness
    random.shuffle(images)

    # Limit results
    images = images[:MAX_RESULTS]

    save_cache(images)
    print(json.dumps(images))
