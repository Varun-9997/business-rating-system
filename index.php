<?php include "conn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Business Listing & Rating System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f8f9fa;
}
.table td, .table th{
    vertical-align:middle;
}
</style>
</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">Business Listing & Rating System</h2>

<div class="d-flex justify-content-between align-items-center mb-3">

<button class="btn btn-primary me-2"
data-bs-toggle="modal"
data-bs-target="#addModal">
Add Business
</button>

<button class="btn btn-dark"
id="viewAllRatingsBtn">
View Ratings
</button>

</div>

<div class="row mb-3">
<div class="col-md-4 ms-auto">
<input type="text" class="form-control" id="searchInput" placeholder="Search businesses">
</div>
</div>

<table class="table table-bordered bg-white">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Name</th>
<th>Address</th>
<th>Phone</th>
<th>Email</th>
<th>Actions</th>
<th>Average Rating</th>
<th>Rate</th>
</tr>
</thead>

<tbody id="businessTable"></tbody>
</table>

<div id="paginationArea"></div>

</div>


<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">

<form id="addBusinessForm">

<div class="modal-header">
<h5>Add Business</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input name="name" class="form-control mb-2" placeholder="Business Name" required>

<input name="address" class="form-control mb-2" placeholder="Address" required>

<input name="phone" class="form-control mb-2" placeholder="Phone" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

</div>

<div class="modal-footer">
<button class="btn btn-success">Save</button>
</div>

</form>

</div>
</div>
</div>


<div class="modal fade" id="editModal">
<div class="modal-dialog">
<div class="modal-content">

<form id="editForm">

<div class="modal-header">
<h5>Edit Business</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" id="editBody"></div>

<div class="modal-footer">
<button class="btn btn-primary">Update</button>
</div>

</form>

</div>
</div>
</div>


<div class="modal fade" id="ratingModal">
<div class="modal-dialog">
<div class="modal-content">

<form id="ratingForm">

<div class="modal-header">
<h5>Submit Rating</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="business_id" id="rating_business_id">

<input name="name" class="form-control mb-2" placeholder="Your Name" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email">

<input name="phone" class="form-control mb-2" placeholder="Phone">

<div id="ratingStars" class="mb-2"></div>

<input type="hidden" name="rating" id="ratingValue">

</div>

<div class="modal-footer">
<button class="btn btn-success">Submit Rating</button>
</div>

</form>

</div>
</div>
</div>

<div class="modal fade" id="viewRatingsModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5>All Ratings</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" id="ratingsListBody">
Loading...
</div>

</div>
</div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/raty/2.9.0/jquery.raty.min.js"></script>


<script>

if($.fn.raty){
    $.fn.raty.defaults.path =
    'https://cdnjs.cloudflare.com/ajax/libs/raty/2.9.0/images';
}

function validateEmail(email){
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function loadBusinesses(page=1){

let search = $("#searchInput").val();

$.get("ajax/fetch_business.php",
{
page:page,
search:search
},
function(data){

let parts = data.split("###pagination###");

$("#businessTable").html(parts[0]);
$("#paginationArea").html(parts[1]);

$('.ratingView').raty({
readOnly:true,
half:true,
score:function(){
return $(this).data("score");
}
});

});

}

$(document).ready(function(){
loadBusinesses();
});

$("#searchInput").keyup(function(){
loadBusinesses(1);
});

$(document).on("click",".page-link-custom",function(){
loadBusinesses($(this).data("page"));
});

$("#addBusinessForm").submit(function(e){

e.preventDefault();

let email = $("input[name='email']", this).val();

if(!validateEmail(email)){
alert("Invalid Email");
return;
}

$.post("ajax/add_business.php",
$(this).serialize(),
function(res){

if(res.trim() != "success"){
    alert(res);
    return;
}

bootstrap.Modal
.getOrCreateInstance(document.getElementById('addModal'))
.hide();

$("#addBusinessForm")[0].reset();

loadBusinesses();

});

});

$(document).on("click",".editBtn",function(){

$.get("ajax/get_business.php",
{id:$(this).data("id")},
function(data){

$("#editBody").html(data);

bootstrap.Modal
.getOrCreateInstance(document.getElementById('editModal'))
.show();

});

});

$(document).on("submit","#editForm",function(e){

e.preventDefault();

$.post("ajax/update_business.php",
$(this).serialize(),
function(){

bootstrap.Modal
.getOrCreateInstance(document.getElementById('editModal'))
.hide();

loadBusinesses();

});

});

$(document).on("click",".deleteBtn",function(){

if(!confirm("Delete this business?")) return;

$.post("ajax/delete_business.php",
{id:$(this).data("id")},
function(){
loadBusinesses();
});

});

$(document).on("click",".rateBtn",function(){

$("#rating_business_id").val($(this).data("id"));

$('#ratingStars').raty('destroy');

$('#ratingStars').raty({
half:true,
click:function(score){
$("#ratingValue").val(score);
}
});

bootstrap.Modal
.getOrCreateInstance(document.getElementById('ratingModal'))
.show();

});

$("#ratingForm").submit(function(e){

e.preventDefault();

let email = $("input[name='email']", this).val();
let phone = $("input[name='phone']", this).val();
let rating = $("#ratingValue").val();

if(email=="" && phone==""){
alert("Email OR Phone required");
return;
}

if(rating==""){
alert("Please select rating");
return;
}

$.post("ajax/save_rating.php",
$(this).serialize(),
function(){

bootstrap.Modal
.getOrCreateInstance(document.getElementById('ratingModal'))
.hide();

$("#ratingForm")[0].reset();

loadBusinesses();

});

});

$("#viewAllRatingsBtn").click(function(){

$("#ratingsListBody").load("ajax/view_rating.php");

bootstrap.Modal
.getOrCreateInstance(document.getElementById('viewRatingsModal'))
.show();

});

</script>

</body>
</html>