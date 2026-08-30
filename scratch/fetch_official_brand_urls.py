import urllib.request
import urllib.parse
import json
import re

brands = [
    {'name_en': 'La Roche-Posay', 'name_ar': 'لاروش بوزيه', 'query': 'La Roche-Posay logo'},
    {'name_en': 'CeraVe', 'name_ar': 'سيرافي', 'query': 'CeraVe logo'},
    {'name_en': 'The Ordinary', 'name_ar': 'ذا أورديناري', 'query': 'The Ordinary skincare logo'},
    {'name_en': 'Vichy', 'name_ar': 'فيشي', 'query': 'Vichy logo'},
    {'name_en': 'Bioderma', 'name_ar': 'بيوديرما', 'query': 'Bioderma logo'},
    {'name_en': 'Neutrogena', 'name_ar': 'نيتروجينا', 'query': 'Neutrogena logo'},
    {'name_en': 'Eucerin', 'name_ar': 'يوسيرين', 'query': 'Eucerin logo'},
    {'name_en': 'Cetaphil', 'name_ar': 'سيتافيل', 'query': 'Cetaphil logo'},
    {'name_en': 'Laneige', 'name_ar': 'لانيج', 'query': 'Laneige logo'},
    {'name_en': 'COSRX', 'name_ar': 'كوزريكس', 'query': 'COSRX logo'},
    {'name_en': "L'Oréal Paris", 'name_ar': 'لوريال باريس', 'query': "L'Oreal Paris logo"},
    {'name_en': 'Clinique', 'name_ar': 'كلينيك', 'query': 'Clinique logo'},
    {'name_en': "Kiehl's", 'name_ar': 'كيلز', 'query': "Kiehl's logo"},
    {'name_en': "Paula's Choice", 'name_ar': 'بولاز تشويس', 'query': "Paula's Choice logo"},
    {'name_en': 'Avène', 'name_ar': 'أفين', 'query': 'Avene logo'},
    {'name_en': 'Medicube', 'name_ar': 'ميديكيوب', 'query': 'Medicube skincare'},
    {'name_en': 'ANUA', 'name_ar': 'أنوا', 'query': 'Anua skincare logo'},
    {'name_en': 'Beauty of Joseon', 'name_ar': 'بيوتي أوف جوسون', 'query': 'Beauty of Joseon logo'},
    {'name_en': 'SKIN1004', 'name_ar': 'سكِن1004', 'query': 'SKIN1004 logo'},
    {'name_en': 'AXIS-Y', 'name_ar': 'أكسيس واي', 'query': 'AXIS-Y logo'},
    {'name_en': 'Dr.Melaxin', 'name_ar': 'د. ميلاكسين', 'query': 'Dr.Melaxin skincare'},
    {'name_en': 'K-SECRET', 'name_ar': 'كي-سيكرت', 'query': 'K-Secret skincare'},
    {'name_en': 'VT Cosmetics', 'name_ar': 'في تي كوزمتكس', 'query': 'VT Cosmetics logo'},
    {'name_en': 'celimax', 'name_ar': 'سيليماكس', 'query': 'celimax skincare'},
    {'name_en': 'Dr. Althea', 'name_ar': 'د. ألثيا', 'query': 'Dr. Althea skincare'},
    {'name_en': 'Arencia', 'name_ar': 'أرينسيا', 'query': 'Arencia cleanser'},
    {'name_en': 'ROUND LAB', 'name_ar': 'راوند لاب', 'query': 'Round Lab logo'},
    {'name_en': 'numbuzin', 'name_ar': 'نمبوزن', 'query': 'numbuzin skincare'},
    {'name_en': 'Abib', 'name_ar': 'أبيب', 'query': 'Abib skincare logo'},
    {'name_en': 'EQQUALBERRY', 'name_ar': 'إيكوالبيري', 'query': 'EQQUALBERRY skincare'},
    {'name_en': 'PURITO', 'name_ar': 'بوريتو', 'query': 'Purito skincare logo'},
    {'name_en': 'BIODANCE', 'name_ar': 'بيو دانس', 'query': 'BIODANCE collagen mask'},
    {'name_en': 'Aestura', 'name_ar': 'أستورا', 'query': 'Aestura skincare'},
    {'name_en': 'Illiyoon', 'name_ar': 'إليون', 'query': 'Illiyoon ceramide'}
]

print(f"Total brands to process: {len(brands)}")
