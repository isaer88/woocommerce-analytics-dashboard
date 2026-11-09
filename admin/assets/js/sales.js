(function(){'use strict';
const root=document.getElementById('wcad-sales-page');if(!root||typeof wcadSalesConfig==='undefined')return;
const $=id=>document.getElementById(id),buttons=[...root.querySelectorAll('[data-days]')];let last=null,controller=null;
const fmt=(v,c)=>new Intl.NumberFormat(undefined,{style:'currency',currency:c}).format(Number(v)||0);
const keys=['gross','net','discounts','refunds','tax','shipping','total','orders'];
function draw(data){last=data;const t=data.totals;keys.forEach(k=>{$('wcad-sales-'+k).textContent=k==='orders'?new Intl.NumberFormat().format(t[k]):fmt(t[k],data.currency)});
const body=$('wcad-sales-rows');body.replaceChildren();data.rows.forEach(r=>{const tr=document.createElement('tr');['date','orders','gross','discounts','refunds','net','tax','shipping','total'].forEach(k=>{const td=document.createElement('td');td.textContent=k==='date'?r[k]:k==='orders'?String(r[k]):fmt(r[k],data.currency);tr.appendChild(td)});body.appendChild(tr)});
$('wcad-sales-notes').textContent=data.notes||'';
window.WCADChart($('wcad-sales-detail-chart'),data.rows.map(r=>r.date),data.rows.map(r=>r.total),data.rows.map(()=>0),v=>fmt(v,data.currency));$('wcad-sales-export').disabled=false;}
async function load(days){if(controller)controller.abort();controller=new AbortController();const c=controller;buttons.forEach(b=>{b.disabled=true;b.classList.toggle('is-active',Number(b.dataset.days)===days)});$('wcad-sales-status').textContent='Loading live sales…';$('wcad-sales-export').disabled=true;
try{const url=new URL(wcadSalesConfig.endpoint);url.searchParams.set('days',days);const res=await fetch(url.toString(),{headers:{'X-WP-Nonce':wcadSalesConfig.nonce},credentials:'same-origin',signal:c.signal});const json=await res.json();if(!res.ok)throw Error(json.message||'HTTP '+res.status);draw(json);$('wcad-sales-status').textContent='Showing '+days+' days · Live WooCommerce data';}catch(e){if(e.name!=='AbortError')$('wcad-sales-status').textContent='Error: '+e.message}finally{if(controller===c)buttons.forEach(b=>b.disabled=false)}}
buttons.forEach(b=>b.addEventListener('click',()=>load(Number(b.dataset.days))));
$('wcad-sales-export').addEventListener('click',()=>{if(!last)return;const fields=['date','orders','gross','discounts','refunds','net','tax','shipping','total'];const esc=v=>'"'+String(v??'').replace(/"/g,'""')+'"';const csv=[fields.join(','),...last.rows.map(r=>fields.map(k=>esc(r[k])).join(','))].join('\r\n');const blob=new Blob(['\ufeff'+csv],{type:'text/csv;charset=utf-8'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download='wcad-sales-'+last.days+'-days.csv';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(url)});
let timer;window.addEventListener('resize',()=>{clearTimeout(timer);timer=setTimeout(()=>{if(last)draw(last)},150)});load([7,30,90].includes(Number(wcadSalesConfig.defaultDays))?Number(wcadSalesConfig.defaultDays):30);
})();
