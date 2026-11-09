/* WCAD v0.2.0 lightweight canvas chart; Chart.js dependency planned for a later iteration. */
(function () {
 'use strict';
 window.WCADChart = function (canvas, labels, current, previous, formatMoney) {
  if (!canvas || !canvas.getContext || !current.length) return;
  const box = canvas.getBoundingClientRect(); const width = Math.max(300, Math.round(box.width)); const height = 300;
  const dpr = window.devicePixelRatio || 1; canvas.width = width*dpr; canvas.height = height*dpr;
  const ctx = canvas.getContext('2d'); ctx.setTransform(dpr,0,0,dpr,0,0); ctx.clearRect(0,0,width,height);
  const pad={l:62,r:18,t:22,b:38}; const cw=width-pad.l-pad.r; const ch=height-pad.t-pad.b;
  const max=Math.max(1,...current,...previous)*1.15;
  ctx.font='12px system-ui, sans-serif';ctx.fillStyle='#64748b';ctx.strokeStyle='#e5eaf0';ctx.lineWidth=1;
  for(let i=0;i<=4;i++){const y=pad.t+ch*i/4;ctx.beginPath();ctx.moveTo(pad.l,y);ctx.lineTo(width-pad.r,y);ctx.stroke();ctx.fillText(formatMoney(max*(1-i/4)),4,y+4);}
  const tickCount=Math.min(5,labels.length);for(let i=0;i<tickCount;i++){const index=Math.round(i*(labels.length-1)/(tickCount-1));const x=pad.l+cw*index/Math.max(1,labels.length-1);ctx.textAlign=i===0?'left':i===tickCount-1?'right':'center';ctx.fillText(labels[index],x,height-12);}ctx.textAlign='left';
  function line(values,color){ctx.beginPath();ctx.strokeStyle=color;ctx.lineWidth=2.5;ctx.lineJoin='round';values.forEach((v,i)=>{const x=pad.l+cw*i/Math.max(1,values.length-1);const y=pad.t+ch*(1-v/max);if(i===0)ctx.moveTo(x,y);else ctx.lineTo(x,y);});ctx.stroke();}
  line(previous,'#94a3b8');line(current,'#2563eb');
 };
})();
