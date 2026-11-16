import urllib.request,urllib.parse,json
from pathlib import Path
q=json.loads(Path('live_audit/data/quiz_questions.json').read_text())['data'];by={x['key']:{o['code']:o for o in x['options']}for x in q}
def payload(primary,secondary,skin,sensitive):
 ops=[by['q1'][primary],by['q4'][secondary or 'none'],by['skin_type'][skin],by['sensitivity']['yes'if sensitive else 'no']]
 form=[('skin_type_id',str(ops[2]['mapped_id'])),('routine_goal_id',str(ops[0]['mapped_id'])),('is_sensitive',str(ops[3]['mapped_id']))]
 if secondary:form.append(('concern_ids[]',str(ops[1]['mapped_id'])))
 form.extend(('option_ids[]',str(x['id']))for x in ops);return form
if __name__=='__main__':
 f=payload('dullness',None,'oily',False);req=urllib.request.Request('https://kcodeskin.com/api/quiz/submit-answers',data=urllib.parse.urlencode(f).encode(),headers={'Content-Type':'application/x-www-form-urlencoded','Accept':'application/json','Accept-Language':'ar'},method='POST');res=urllib.request.urlopen(req,timeout=40);raw=res.read();Path('live_audit/data/quiz_probe.json').write_bytes(raw);d=json.loads(raw);print('status',res.status,'bytes',len(raw));print('top',list(d));x=d.get('data',{});print('keys',list(x));print('diagnosis',x.get('diagnosis'));print('sample',str(x.get('primary_routine',[None])[0])[:5500]);print('support',[(p.get('sku')or(p.get('product')or{}).get('sku')) for p in x.get('routine_support',[])])
