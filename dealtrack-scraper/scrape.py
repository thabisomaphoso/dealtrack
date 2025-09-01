import asyncio
import sqlite3
import random
from datetime import datetime, timezone
import pandas as pd
from playwright.async_api import async_playwright

DB_PATH = "dealtrack.db"
SEARCH_TERM = "NESTLÉ NIDO 3+"

STORES = [
    {"name": "Clicks", "url": "https://www.clicks.co.za"},
    {"name": "Game", "url": "https://www.game.co.za"},
    {"name": "Woolworths", "url": "https://www.woolworths.co.za"},
    {"name": "Pick n Pay", "url": "https://www.pnp.co.za"},
    {"name": "Takealot", "url": "https://www.takealot.com"},
    {"name": "Dischem", "url": "https://www.dischem.co.za"},
    {"name": "Baby City", "url": "https://www.babycity.co.za"},
    {"name": "Makro", "url": "https://www.makro.co.za"},
    {"name": "Checkers", "url": "https://www.checkers.co.za"},
    {"name": "Shoprite", "url": "https://www.shoprite.co.za"}
]

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36"
}

# ---------------- Database ----------------
def init_db():
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute("""
    CREATE TABLE IF NOT EXISTS products_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        store TEXT,
        title TEXT,
        price REAL,
        currency TEXT,
        sku TEXT,
        url TEXT,
        image_url TEXT,
        scraped_at TEXT
    )
    """)
    conn.commit()
    conn.close()

def save_product(store, data):
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute("""
    INSERT INTO products_history (store, title, price, currency, sku, url, image_url, scraped_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    """, (
        store,
        data.get("title"),
        data.get("price"),
        data.get("currency"),
        data.get("sku"),
        data.get("url"),
        data.get("image_url"),
        datetime.now(timezone.utc).isoformat()
    ))
    conn.commit()
    conn.close()

# ---------------- Price parsing ----------------
import re
def parse_price(text):
    if not text: return (None, None)
    m = re.search(r"([R£$€]\s?[\d.,]+)", text)
    if not m: return (None, None)
    raw = m.group(0)
    currency = re.sub(r"[\d.,\s]", "", raw)
    num = re.sub(r"[^\d.,]", "", raw).replace(",", "")
    try:
        return float(num), currency
    except:
        return None, currency

# ---------------- Scraper ----------------
async def scrape_store(playwright, store_name, url, search_term=SEARCH_TERM):
    browser = await playwright.chromium.launch(headless=False)
    page = await browser.new_page(extra_http_headers=HEADERS)
    try:
        await page.goto(url, timeout=90000)
        await asyncio.sleep(random.uniform(2,4))

        # ---------------- Search ----------------
        try:
            search_input = page.locator("input[type='search'], input[name='q']").first
            await search_input.fill(search_term)
            await search_input.press("Enter")
            await page.wait_for_load_state("networkidle")
            await asyncio.sleep(random.uniform(2,4))
        except Exception as e:
            print(f"⚠️ {store_name}: Could not perform search: {e}")

        # ---------------- Extract first product ----------------
        try:
            first_product = page.locator(".search-result-item, .product-card, .product-listing").first
            title = await first_product.locator("h1, h2, .product-title").inner_text()
        except: title = None
        try:
            price_text = await first_product.locator(".price, .currency, .product-price").inner_text()
        except: price_text = None
        try:
            sku = await first_product.locator(".sku, .product-code").inner_text()
        except: sku = None
        try:
            image_url = await first_product.locator("img").get_attribute("src")
        except: image_url = None

        price, currency = parse_price(price_text)
        data = {
            "title": title.strip() if title else None,
            "price": price,
            "currency": currency,
            "sku": sku.strip() if sku else None,
            "url": url,
            "image_url": image_url
        }
        save_product(store_name, data)
        print(f"✅ {store_name}: {title} - {price} {currency}")

    except Exception as e:
        print(f"❌ {store_name} scrape failed: {e}")
    finally:
        await browser.close()

# ---------------- CSV export ----------------
def export_csv():
    conn = sqlite3.connect(DB_PATH)
    df = pd.read_sql_query("SELECT * FROM products_history ORDER BY scraped_at DESC", conn)
    df.to_csv("dealtrack_prices_history.csv", index=False)
    conn.close()
    print("✅ CSV export complete: dealtrack_prices_history.csv")

# ---------------- Main ----------------
async def main_async():
    init_db()
    async with async_playwright() as p:
        for store in STORES:
            await scrape_store(p, store["name"], store["url"])
            await asyncio.sleep(random.uniform(1,3))
    export_csv()

# ---------------- Periodic scraping ----------------
async def periodic_scrape(interval_hours=6):
    while True:
        await main_async()
        print(f"⏳ Waiting {interval_hours} hours until next scrape...")
        await asyncio.sleep(interval_hours*3600)

# ---------------- Run ----------------
if __name__ == "__main__":
    try:
        asyncio.run(periodic_scrape(interval_hours=6))
    except RuntimeError:
        loop = asyncio.get_event_loop()
        loop.run_until_complete(periodic_scrape(interval_hours=6))
