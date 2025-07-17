/* === HELPERS (bootstrap‑safe) === */
const qs  =(s,c=document)=>c.querySelector(s),
      qsa =(s,c=document)=>[...c.querySelectorAll(s)];

/* === ELEMENTS === */
const body        = document.body,
      sidebar     = qs('#sb-sidebar'),
      collapseBtn = qs('.sb-toggler'),
      themeBtn    = qs('#sb-topbarThemeBtn'),
      themeIcon   = qs('#sb-themeIcon'),
      hamburger   = qs('#sb-mobileHamburger'),
      profileBtn  = qs('.sb-profile-btn'),
      profileDropdown = qs('.sb-profile-dropdown');

/* === PAGE SELECTION === */
sidebar.addEventListener('click',e=>{
  const li=e.target.closest('.sb-item:not(.sb-has-sub)');
  if(!li) return;
  qs('[aria-current="page"]',sidebar)?.removeAttribute('aria-current');
  li.setAttribute('aria-current','page');
});

/* === TOOLTIP === */
const tooltip=(()=>{
  let tip,move;
  const create=()=>{tip=document.createElement('div');tip.className='sb-tooltip';document.body.appendChild(tip);};
  const show=e=>{
    if(!sidebar.classList.contains('sb-collapsed'))return;
    const t=e.currentTarget.getAttribute('data-tooltip');if(!t)return;
    tip??create();tip.textContent=t;tip.classList.add('sb-show');pos(e);
    document.addEventListener('mousemove',move=pos,{passive:true});
  };
  const hide=()=>{tip?.classList.remove('sb-show');document.removeEventListener('mousemove',move);};
  const pos=e=>{
    const r=tip.getBoundingClientRect(),pad=12;
    let x=e.clientX+pad,y=e.clientY+pad;
    if(x+r.width>innerWidth)x=e.clientX-r.width-pad;
    if(y+r.height>innerHeight)y=e.clientY-r.height-pad;
    tip.style.left=x+'px';tip.style.top=y+'px';
  };
  const bind=()=>{qsa('#sb-sidebar li[data-tooltip]').forEach(li=>{
    li.onmouseenter=show;li.onmouseleave=hide;li.onfocus=show;li.onblur=hide;
  });};
  bind();
  collapseBtn.addEventListener('click',()=>setTimeout(()=>{bind();hide();},310));
  return{hide};
})();

/* === COLLAPSE === */
collapseBtn.addEventListener('click',toggleCollapse);
collapseBtn.addEventListener('keydown',e=>{
  if(['Enter',' '].includes(e.key)){e.preventDefault();toggleCollapse();}
});
function toggleCollapse(){
  sidebar.classList.toggle('sb-collapsed');
  body.classList.toggle('sb-sidebar-collapsed');
  collapseBtn.setAttribute('aria-pressed',sidebar.classList.contains('sb-collapsed'));
  tooltip.hide();
}

/* === SUBMENUS === */
qsa('.sb-has-sub').forEach(parent=>{
  const submenu=parent.nextElementSibling;
  const toggle = ()=>{
    submenu.classList.toggle('sb-open');
    parent.setAttribute('aria-expanded',submenu.classList.contains('sb-open'));
  };
  parent.addEventListener('click',toggle);
  parent.addEventListener('keydown',e=>{
    if(['Enter',' '].includes(e.key)){e.preventDefault();toggle();}
  });
});

/* === THEME === */
const setTheme=d=>{
  body.classList.toggle('dark',d);
  themeIcon.textContent=d?'☀️':'🌙';
  localStorage.theme=d?'dark':'light';
};
setTheme((localStorage.theme|| (matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'))==='dark');
matchMedia('(prefers-color-scheme:dark)').addEventListener('change',e=>!localStorage.theme&&setTheme(e.matches));
themeBtn.addEventListener('click',()=>setTheme(!body.classList.contains('dark')));

/* === HAMBURGER === */
const openSidebar  =()=>{sidebar.classList.add('sb-active');hamburger.classList.add('sb-active','sb-hide');};
const closeSidebar =()=>{sidebar.classList.remove('sb-active');hamburger.classList.remove('sb-active','sb-hide');};
hamburger.addEventListener('click',()=>sidebar.classList.contains('sb-active')?closeSidebar():openSidebar());

/* === PROFILE DROPDOWN === */
profileBtn.addEventListener('click',()=>{
  profileDropdown.classList.toggle('open');
});
document.addEventListener('click',e=>{
  if(!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)){
    profileDropdown.classList.remove('open');
  }
});

/* === RESIZE === */
let w=innerWidth;
addEventListener('resize',()=>{
  if((innerWidth>768&&w<=768)||(innerWidth<=768&&w>768)){
    if(innerWidth>768)closeSidebar();
  }
  w=innerWidth;
});