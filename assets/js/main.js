document.addEventListener('DOMContentLoaded', ()=>{
  // année pied de page
  const y = document.getElementById('year'); if (y) y.textContent = new Date().getFullYear();

  // nav active
  const path = (location.pathname.split('/').pop() || 'index.html').toLowerCase();
  document.querySelectorAll('.nav a').forEach(a=>{
    const href = a.getAttribute('href').toLowerCase();
    if (href === path) a.classList.add('active');
  });

  // Lightbox (pages avec .gallery)
  const thumbs = Array.from(document.querySelectorAll('.thumb img'));
  if (!thumbs.length) return;

  let idx = -1;
  const lb = document.createElement('div');
  lb.style = 'position:fixed;inset:0;background:rgba(0,0,0,.85);display:none;align-items:center;justify-content:center;z-index:9999;';
  lb.innerHTML = `
    <button id="lbPrev" style="position:fixed;left:20px;top:50%;transform:translateY(-50%);background:#0e1a31;border:1px solid #27314d;color:#eaf2ff;padding:10px 14px;border-radius:12px;cursor:pointer">‹</button>
    <img id="lbImg" style="max-width:92vw;max-height:86vh;border:1px solid #27314d;border-radius:12px;box-shadow:0 15px 40px rgba(0,0,0,.6)" alt="">
    <button id="lbNext" style="position:fixed;right:20px;top:50%;transform:translateY(-50%);background:#0e1a31;border:1px solid #27314d;color:#eaf2ff;padding:10px 14px;border-radius:12px;cursor:pointer">›</button>
    <button id="lbClose" style="position:fixed;right:20px;top:20px;background:#0e1a31;border:1px solid #27314d;color:#eaf2ff;padding:10px 14px;border-radius:12px;cursor:pointer">✕</button>`;
  document.body.appendChild(lb);

  const lbImg = lb.querySelector('#lbImg');
  const open = i => { idx=i; lbImg.src = thumbs[idx].dataset.full || thumbs[idx].src; lb.style.display='flex'; document.body.style.overflow='hidden'; };
  const close = ()=>{ lb.style.display='none'; document.body.style.overflow=''; };
  const prev = ()=> open((idx>0?idx-1:thumbs.length-1));
  const next = ()=> open((idx<thumbs.length-1?idx+1:0));

  thumbs.forEach((im,i)=> im.addEventListener('click', ()=> open(i)));
  lb.querySelector('#lbClose').onclick = close;
  lb.querySelector('#lbPrev').onclick = prev;
  lb.querySelector('#lbNext').onclick = next;
  lb.addEventListener('click', e=>{ if(e.target===lb) close(); });
  document.addEventListener('keydown', e=>{
    if(lb.style.display!=='flex') return;
    if(e.key==='Escape') close();
    if(e.key==='ArrowLeft') prev();
    if(e.key==='ArrowRight') next();
  });
});
// Carrousel auto sur la page d’accueil
document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.carousel img');
  if (!slides.length) return;

  let current = 0;
  setInterval(() => {
    slides[current].classList.remove('active');
    current = (current + 1) % slides.length;
    slides[current].classList.add('active');
  }, 5000); // toutes les 5 secondes
});
