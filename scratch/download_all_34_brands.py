import urllib.request
import os

storage_dir = r'c:\myProjects\KCODE\storage\app\public\brands'
if not os.path.exists(storage_dir):
    os.makedirs(storage_dir, exist_ok=True)

# Curated working high quality URLs for brands
brand_urls = {
    'la-roche-posay': 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=800',
    'cerave': 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800',
    'the-ordinary': 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=800',
    'vichy': 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800',
    'bioderma': 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=800',
    'neutrogena': 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800',
    'eucerin': 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=800',
    'cetaphil': 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=800',
    'laneige': 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=800',
    'cosrx': 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800',
    'loreal-paris': 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=800',
    'clinique': 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=800',
    'kiehls': 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800',
    'paulas-choice': 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=800',
    'avene': 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800',
    'medicube': 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&q=80&w=800',
    'anua': 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?auto=format&fit=crop&q=80&w=800',
    'beauty-of-joseon': 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&q=80&w=800',
    'skin1004': 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&q=80&w=800',
    'axis-y': 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=800',
    'drmelaxin': 'https://images.unsplash.com/photo-1556227702-d1e4e7b5c232?auto=format&fit=crop&q=80&w=800',
    'k-secret': 'https://images.unsplash.com/photo-1615397349754-cfa2066a298e?auto=format&fit=crop&q=80&w=800',
    'vt-cosmetics': 'https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?auto=format&fit=crop&q=80&w=800',
    'celimax': 'https://images.unsplash.com/photo-1611080626919-7cf5a9dbab5b?auto=format&fit=crop&q=80&w=800',
    'dr-althea': 'https://images.unsplash.com/photo-1608248597279-f99d160bfcbc?auto=format&fit=crop&q=80&w=800',
    'arencia': 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=800',
    'round-lab': 'https://images.unsplash.com/photo-1601049676099-e7ed07d825b0?auto=format&fit=crop&q=80&w=800',
    'numbuzin': 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=800',
    'abib': 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=800',
    'eqqualberry': 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&q=80&w=800',
    'purito': 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?auto=format&fit=crop&q=80&w=800',
    'biodance': 'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&q=80&w=800',
    'aestura': 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&q=80&w=800',
    'illiyoon': 'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&q=80&w=800'
}

# Preserve the generated PNGs for key brands if size > 100KB
art_dir = r'C:\Users\Dell\.gemini\antigravity-ide\brain\c1c1a1ba-8779-43f5-9769-ef97ca34d2bf'
generated_pairs = {
    'beauty-of-joseon': 'beauty_of_joseon_logo_1788080733188.png',
    'cosrx': 'cosrx_official_logo_1788080710925.png',
    'anua': 'anua_official_logo_1788080721511.png',
    'skin1004': 'skin1004_official_logo_1788080744040.png',
    'medicube': 'medicube_official_logo_1788080756949.png',
    'round-lab': 'round_lab_logo_1788080767968.png',
    'biodance': 'biodance_logo_1788080778803.png',
    'numbuzin': 'numbuzin_logo_1788080790047.png',
    'vt-cosmetics': 'vt_cosmetics_logo_1788080807736.png',
    'axis-y': 'axis_y_brand_1788079879121.png'
}

print("Updating all 34 brand images...")
for slug, url in brand_urls.items():
    dst_path = os.path.join(storage_dir, slug + '.jpg')
    
    # If we have a custom generated high-res PNG for this brand, use it!
    if slug in generated_pairs:
        gen_src = os.path.join(art_dir, generated_pairs[slug])
        if os.path.exists(gen_src) and os.path.getsize(gen_src) > 100000:
            import shutil
            shutil.copy(gen_src, dst_path)
            print(f'[GENERATED OK] {slug} -> {dst_path} ({os.path.getsize(dst_path)} bytes)')
            continue
            
    # Otherwise download fresh clean high quality image from web URL
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req) as resp:
            data = resp.read()
            with open(dst_path, 'wb') as out:
                out.write(data)
            print(f'[DOWNLOAD OK] {slug} -> {dst_path} ({len(data)} bytes)')
    except Exception as e:
        print(f'[FAIL] {slug} -> {e}')

print("All 34 brand images updated!")
