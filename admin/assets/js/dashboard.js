(function () {
 'use strict';
 const root=document.getElementById('wcad-dashboard');if(!root||typeof wcadConfig==='undefined')return;
 const $=id=>document.getElementById(id);const buttons=Array.from(root.querySelectorAll('[data-days]'));
 let last=null;let controller=null;
 const money=(value,currency='USD')=>new Intl.NumberFormat(undefined,{style:'currency',currency,maximumFractionDigits:0}).format(value);
 function draw(data){last=data;const k=data.kpis;const fmt=v=>money(v,data.currency);
  $('wcad-sales').textContent=fmt(k.sales);$('wcad-orders').textContent=new Intl.NumberFormat().format(k.orders);
  $('wcad-aov').textContent=new Intl.NumberFormat(undefined,{style:'currency',currency:data.currency}).format(k.aov);
  $('wcad-refunds').textContent=fmt(k.refunds);$('wcad-change').textContent=k.change===null?'No previous-period sales':(k.change>=0?'+':'')+k.change+'% vs previous period';
  $('wcad-notes').textContent=data.notes||'';
  $('wcad-products-body').replaceChildren();if(!data.products.length){const tr=document.createElement('tr');const td=document.createElement('td');td.colSpan=3;td.textContent='No products sold in this period.';tr.appendChild(td);$('wcad-products-body').appendChild(tr);}data.products.forEach(p=>{const tr=document.createElement('tr');[p.name,String(p.quantity),fmt(p.revenue)].forEach(v=>{const td=document.createElement('td');td.textContent=v;tr.appendChild(td);});$('wcad-products-body').appendChild(tr);});
  window.WCADChart($('wcad-sales-chart'),data.chart.labels,data.chart.sales,data.chart.previous,v=>fmt(v));
 }
 async function load(days){if(controller)controller.abort();controller=new AbortController();const current=controller;
  buttons.forEach(b=>{const active=Number(b.dataset.days)===days;b.classList.toggle('is-active',active);b.setAttribute('aria-pressed',String(active));b.disabled=true;});$('wcad-status').textContent='Loading store data…';
  try{const url=new URL(wcadConfig.endpoint);url.searchParams.set('days',days);const response=await fetch(url.toString(),{headers:{'X-WP-Nonce':wcadConfig.nonce},credentials:'same-origin',signal:current.signal});if(!response.ok)throw Error('HTTP '+response.status);const data=await response.json();draw(data);$('wcad-status').textContent='Showing '+days+' days · Live WooCommerce data';}
  catch(e){if(e.name!=='AbortError'){$('wcad-status').textContent=wcadConfig.error+' ('+e.message+')';}}
  finally{if(controller===current)buttons.forEach(b=>b.disabled=false);}
 }
 buttons.forEach(b=>b.addEventListener('click',()=>load(Number(b.dataset.days))));let resizeTimer;window.addEventListener('resize',()=>{clearTimeout(resizeTimer);resizeTimer=setTimeout(()=>{if(last)draw(last);},150);});load([7,30,90].includes(Number(wcadConfig.defaultDays))?Number(wcadConfig.defaultDays):30);
})();
