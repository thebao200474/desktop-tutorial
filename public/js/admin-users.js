const state={token:localStorage.getItem('adminToken')||''};
if(!state.token) window.location.href='/admin-login.html';
const usersTable=document.getElementById('users-table');
async function api(path){const r=await fetch(path,{headers:{Authorization:`Bearer ${state.token}`}});if(r.status===401){localStorage.removeItem('adminToken');window.location.href='/admin-login.html';return{};}return r.json();}
document.getElementById('admin-logout').addEventListener('click',()=>{localStorage.removeItem('adminToken');window.location.href='/admin-login.html';});
async function loadUsers(){const data=await api('/api/admin/users');usersTable.innerHTML=(data.users||[]).map(u=>`<tr><td>${u.MaDocGia}</td><td>${u.HoLot} ${u.Ten}</td><td>${u.Email||''}</td><td>${u.DienThoai||''}</td><td><span class="badge ${u.TrangThai==='Đang mượn'?'text-bg-warning':'text-bg-success'}">${u.TrangThai}</span></td></tr>`).join('');}
loadUsers();
