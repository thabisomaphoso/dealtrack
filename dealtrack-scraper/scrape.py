import asyncio
import csv
from datetime import datetime
from playwright.async_api import async_playwright

# List of retailers with their base URLs and fallback selectors
RETAILERS = {
    "Clicks": {
        "url": "https://clicks.co.za/",
        "selectors": [
            "input#searchTerm",
            "input[placeholder*='Search']",
            "input[type='search']"
        ]
    },
    "Game": {
        "url": "https://www.game.co.za/",
        "selectors": [
            "input.js-site-search-input",
            "input[placeholder*='Search']",
            "input[name='q']"
        ]
    },
    "Woolworths": {
        "url": "https://www.woolworths.co.za/",
        "selectors": [
            "input#search",
            "input[aria-label='Search']",
            "input[placeholder*='Search']"
        ]
    },
    "Pick n Pay": {
        "url": "https://www.pnp.co.za/",
        "selectors": [
            "input#search",
            "input[placeholder*='Search']",
            "input[type='search']"
        ]
    },
    "Takealot": {
        "url": "https://www.takealot.com/",
        "selectors": [
            "input[name='search']",
            "input[placeholder*='Search']"
        ]
    },
    "Dischem": {
        "url": "https://www.dischem.co.za/",
        "selectors": [
            "input#search",
            "input[placeholder*='Search']"
        ]
    },
    "Baby City": {
        "url": "https://www.babycity.co.za/",
        "selectors": [
            "input#search",
            "input[placeholder*='Search']"
        ]
    },
    "Makro": {
        "url": "https://www.makro.co.za/",
        "selectors": [
            "input#search",
            "input[placeholder*='Search']"
        ]
    },
    "Checkers": {
        "url": "https://www.checkers.co.za/",
        "selectors": [
            "input#search",
            "input[placeholder*='Search']"
        ]
    },
    "Shoprite": {
        "url": "https://www.shoprite.co.za/",
        "selectors": [
            "input#search",
            "input[placeholder*='Search']"
        ]
    },
}

# Product to search for
SEARCH_PRODUCT = "NESTLÉ NIDO 3+"

# Helper: Try multiple selectors
async def safe_search(page, selectors, query):
    for sel in selectors:
        try:
            box = await page.wait_for_selector(sel, timeout=8000)
            await box.fill(query)
            await box.press("Enter")
            return True
        except Exception:
            continue
    return False

# Main scrape function
async def scrape():
    results = []
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        for retailer, config in RETAILERS.items():
            try:
                page = await browser.new_page()
                await page.goto(config["url"], timeout=60000)
                
                ok = await safe_search(page, config["selectors"], SEARCH_PRODUCT)
                if not ok:
                    print(f"⚠️ {retailer}: Search bar not found")
                    results.append([retailer, None, None, None])
                    continue
                
                # Wait for results page (basic placeholder logic)
                await page.wait_for_timeout(5000)
                
                # Try to grab first result details (very site-specific in reality)
                title = await page.title()
                results.append([retailer, SEARCH_PRODUCT, "Found", title])
                print(f"✅ {retailer}: Search executed")
            
            except Exception as e:
                print(f"⚠️ {retailer}: Error {e}")
                results.append([retailer, None, None, None])
        
        await browser.close()

    # Save results to CSV
    filename = f"dealtrack_prices_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
    with open(filename, "w", newline="", encoding="utf-8") as f:
        writer = csv.writer(f)
        writer.writerow(["Retailer", "Product", "Status", "Details"])
        writer.writerows(results)
    
    print(f"✅ CSV export complete: {filename}")

if __name__ == "__main__":
    asyncio.run(scrape())
