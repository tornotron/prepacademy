var actionData = {
    'action': 'fetch_user_data', //calls wp_ajax_nopriv_ajaxlogout
    'ajaxsecurity': ajax_object.logout_nonce
  }
var form_data = new FormData();
for (var key in actionData ) {
form_data.append(key, actionData[key]);
}

var userMeta = {};
var xmlHttp = new XMLHttpRequest();
xmlHttp.open('POST', ajax_object.ajax_url); 
xmlHttp.send(form_data);

xmlHttp.onreadystatechange = function()
{
if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
{
userMeta = JSON.parse(xmlHttp.responseText);
if(!(Object.keys(userMeta).length == 0)){
addLogoutDropDownHtml(userMeta);
}
}
};

function addLogoutDropDownHtml(userMeta) {
var accountIcon = document.getElementsByClassName("ast-header-account-wrap")[0];
var dropDown= document.createElement("div");
dropDown.classList.add("torno-account-logout-dropdown");
dropDown.insertAdjacentHTML('afterbegin', `
<div class="torno-profile-container">
<div class="torno-profile-container--a">
<img src="${userMeta.img_url}" alt="profile picture">
</div>
<div class="torno-profile-container--b">
<h5>${userMeta.username}</h5>
<p>${userMeta.email}</p>
<a href="#" onclick="logOut()" class="torno-logout-button">LOGOUT</a>
</div>
</div>
`);
accountIcon.addEventListener("mouseover", mouseOver, false);
dropDown.addEventListener("mouseover", mouseOver, false);
accountIcon.addEventListener("mouseout", mouseOut, false);
dropDown.addEventListener("mouseout", mouseOut, false);
accountIcon.parentNode.insertBefore(dropDown, accountIcon.nextSibling);

function mouseOver()
{ 
if(window.t){
clearTimeout(window.t);
dropDown.style.display = "block";
} else {
dropDown.style.display = "block";
} 
}

function mouseOut()
{
window.t = setTimeout(function() {
dropDown.style.display = "none";
}, 1000) 
}

};

function logOut() 
{
action = 'custom_ajax_logout';
sendAjaxRequest(action);
}

function sendAjaxRequest(action) {
var actionData = {
      'action': action, //calls wp_ajax_nopriv_ajaxlogout
      'ajaxsecurity': ajax_object.logout_nonce
    }
var form_data = new FormData();
for (var key in actionData ) {
form_data.append(key, actionData[key]);
}

var xmlHttp = new XMLHttpRequest();
xmlHttp.onreadystatechange = function()
{
if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
{
window.location = ajax_object.home_url;
}
};
xmlHttp.open('POST', ajax_object.ajax_url); 
xmlHttp.send(form_data);

}