import asyncio
import json
import os
import sys
import uuid
from playwright.async_api import async_playwright

if len(sys.argv) != 2:
    print(json.dumps({"error": "Expected 1 argument1: username"}))
    sys.exit(1)

QUERY = "https://artpromptgenerator.com/"
CACHE_FOLDER = os.path.join(os.path.dirname(__file__), "..", "cache")
os.makedirs(CACHE_FOLDER, exist_ok=True)  # create folder if missing

CACHE_FILE = os.path.join(CACHE_FOLDER, f"{sys.argv[1]}_prompts_cache.json")

def save_cache(images):
    with open(CACHE_FILE, "w", encoding="utf-8") as f:
        json.dump(images, f)

def load_cache():
    if os.path.exists(CACHE_FILE):
        with open(CACHE_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    return None

async def get_prompts():
    async with async_playwright() as p:
        results = []
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()

        await page.goto(QUERY, timeout=30000)

        # --- Handle cookie consent safely ---
        try:
            await page.wait_for_selector(".fc-consent-root", timeout=5000)

            accept_buttons = [
                "button[aria-label='Accept']",
                "button:has-text('Accept')",
                "button:has-text('I Accept')",
                "button:has-text('Agree')",
            ]

            for selector in accept_buttons:
                if await page.locator(selector).count() > 0:
                    await page.click(selector, timeout=3000)
                    break
        except:
            pass

        # Remove any remaining overlays
        await page.evaluate("""
        document.querySelectorAll(
          '.fc-consent-root, .fc-dialog-overlay, .fc-footer'
        ).forEach(el => el.remove());
        """)

        for i in range(0, 10):
            # Click generate
            await page.wait_for_selector("#pt-newBtn")
            await page.click("#pt-newBtn", force=True)

            # Extract prompts
            labels = await page.locator(".prompt-tool__label").all_inner_texts()
            values = await page.locator(".prompt-tool__value").all_inner_texts()

            # Build dict
            prompts = {}
            prompts["id"] = str(uuid.uuid4())
            for label, value in zip(labels, values):
                prompts[label.strip()] = value.strip()

            results.append(prompts)

        await browser.close()
        return results



# ---- ENTRY POINT ----
if __name__ == "__main__":
    cached = load_cache()
    if cached:
        print(json.dumps(cached))
        exit()

    prompts = asyncio.run(get_prompts())
    save_cache(prompts)

    print(json.dumps(prompts))

