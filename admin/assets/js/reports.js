(function(){'use strict';
const root=document.getElementById('wcad-reports-page');if(!root||typeof wcadReportsConfig==='undefined')return;
const $=id=>document.getElementById(id);let last=null,controller=null;
const today=()=>{const d=new Date();return [d.getFullYear(),String(d.getMonth()+1).padStart(2,'0'),String(d.getDate()).padStart(2,'0')].join('-')};
const dateShift=(days)=>{const d=new Date();d.setDate(d.getDate()+days);return [d.getFullYear(),String(d.getMonth()+1).padStart(2,'0'),String(d.getDate()).padStart(2,'0')].join('-')};
const money=(n,c)=>new Intl.NumberFormat(undefined,{style:'currency',currency:c}).format(Number(n)||0);
const num=n=>new Intl.NumberFormat().format(Number(n)||0);
function quick(days){$('wcad-report-start').value=dateShift(1-days);$('wcad-report-end').value=today();}
root.querySelectorAll('[data-days]').forEach(b=>b.addEventListener('click',()=>{quick(Number(b.dataset.days));load()}));
function metric(k,v,prev,c){$('wcad-report-'+k).textContent=k==='revenue'?money(v,c):num(v);const el=$('wcad-report-'+k+'-change');if(prev===null){el.textContent='';return;}const delta=Number(v)-Number(prev);el.textContent=(delta>=0?'+':'')+(k==='revenue'?money(delta,c):num(delta))+' vs previous period';}
function draw(data){last=data;const t=data.current.totals,p=data.previous?.totals||null;
['orders','revenue','units','customers'].forEach(k=>metric(k,t[k],p?p[k]:null,data.currency));
const body=$('wcad-report-rows');body.replaceChildren();data.current.rows.forEach(r=>{const tr=document.createElement('tr');['date','orders','revenue','units','customers'].forEach(k=>{const td=document.createElement('td');td.textContent=k==='revenue'?money(r[k],data.currency):String(r[k]);tr.appendChild(td)});body.appendChild(tr)});
const key=data.type==='customers'?'customers':data.type==='products'?'units':data.type==='orders'?'orders':'revenue';
window.WCADChart($('wcad-report-chart'),data.current.rows.map(r=>r.date),data.current.rows.map(r=>r[key]),data.current.rows.map(()=>0),v=>key==='revenue'?money(v,data.currency):num(v));
$('wcad-report-notes').textContent=data.notes||'';$('wcad-report-export').disabled=false;}
async function load(){if(controller)controller.abort();controller=new AbortController();const c=controller;const type=$('wcad-report-type').value,start=$('wcad-report-start').value,end=$('wcad-report-end').value;
if(!start||!end||start>end){$('wcad-report-status').textContent='Select a valid date range.';return;}
$('wcad-report-run').disabled=true;$('wcad-report-export').disabled=true;$('wcad-report-status').textContent='Generating report…';
try{const url=new URL(wcadReportsConfig.endpoint);Object.entries({type,start,end,compare:$('wcad-report-compare').checked?'true':'false'}).forEach(([k,v])=>url.searchParams.set(k,v));const res=await fetch(url.toString(),{headers:{'X-WP-Nonce':wcadReportsConfig.nonce},credentials:'same-origin',signal:c.signal});const json=await res.json();if(!res.ok)throw Error(json.message||'HTTP '+res.status);draw(json);$('wcad-report-status').textContent='Report: '+type+' · '+start+' to '+end;}catch(e){if(e.name!=='AbortError')$('wcad-report-status').textContent='Error: '+e.message;}finally{if(controller===c)$('wcad-report-run').disabled=false;}}
$('wcad-report-run').addEventListener('click',load);
$('wcad-report-export').addEventListener('click',()=>{if(!last)return;const keys=['date','orders','revenue','units','customers'];const esc=v=>'"'+String(v??'').replace(/"/g,'""')+'"';const csv=[keys.join(','),...last.current.rows.map(r=>keys.map(k=>esc(r[k])).join(','))].join('\r\n');const blob=new Blob(['\ufeff'+csv],{type:'text/csv;charset=utf-8'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download='wcad-'+last.type+'-'+last.start+'-'+last.end+'.csv';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(url)});
const storage='wcad_reports_presets_v080';function presets(){try{return JSON.parse(localStorage.getItem(storage)||'{}')}catch(e){return {}}}
function showPresets(){const select=$('wcad-report-presets');select.replaceChildren(new Option('Choose preset',''));Object.keys(presets()).sort().forEach(name=>select.add(new Option(name,name)));}
$('wcad-report-save').addEventListener('click',()=>{const name=window.prompt('Preset name (max 60 characters)');if(!name||!name.trim())return;const data=presets();data[name.trim().slice(0,60)]={type:$('wcad-report-type').value,start:$('wcad-report-start').value,end:$('wcad-report-end').value,compare:$('wcad-report-compare').checked};try{localStorage.setItem(storage,JSON.stringify(data));showPresets();$('wcad-report-status').textContent='Preset saved in this browser.';}catch(e){$('wcad-report-status').textContent='Browser storage unavailable.';}});
$('wcad-report-presets').addEventListener('change',e=>{const p=presets()[e.target.value];if(!p)return;$('wcad-report-type').value=p.type;$('wcad-report-start').value=p.start;$('wcad-report-end').value=p.end;$('wcad-report-compare').checked=!!p.compare;load()});
$('wcad-report-delete').addEventListener('click',()=>{const name=$('wcad-report-presets').value;if(!name)return;const data=presets();delete data[name];localStorage.setItem(storage,JSON.stringify(data));showPresets()});
let resize;window.addEventListener('resize',()=>{clearTimeout(resize);resize=setTimeout(()=>{if(last)draw(last)},150)});
quick(30);showPresets();load();
})();
