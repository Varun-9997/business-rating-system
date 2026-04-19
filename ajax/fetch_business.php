<?php
require_once __DIR__ . "/../conn.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = trim($_GET['search'] ?? '');

$limit = 5;
$offset = ($page - 1) * $limit;

$where = "";
$params = [];

if($search != ""){
$where = " WHERE b.name LIKE ? OR b.address LIKE ? OR b.phone LIKE ? OR b.email LIKE ? ";
$searchValue = "%$search%";
$params = [$searchValue,$searchValue,$searchValue,$searchValue];
}

$countSql = "
SELECT COUNT(*) as total
FROM businesses b
$where
";

$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$totalRows = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

$totalPages = ceil($totalRows / $limit);

$sql = "
SELECT 
b.id,
b.name,
b.address,
b.phone,
b.email,
IFNULL(AVG(r.rating),0) as avg_rating
FROM businesses b
LEFT JOIN ratings r ON r.business_id = b.id
$where
GROUP BY b.id,b.name,b.address,b.phone,b.email
LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$sr = $offset + 1;

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
?>

<tr id="row<?=$row['id']?>">

<td><?=$sr++?></td>
<td><?=$row['name']?></td>
<td><?=$row['address']?></td>
<td><?=$row['phone']?></td>
<td><?=$row['email']?></td>

<td>
<button class="btn btn-primary btn-sm editBtn" data-id="<?=$row['id']?>">Edit</button>
<button class="btn btn-danger btn-sm deleteBtn" data-id="<?=$row['id']?>">Delete</button>
</td>

<td>
<div class="ratingView"
data-score="<?=$row['avg_rating']?>"></div>
</td>

<td>
<button class="btn btn-success btn-sm rateBtn"
data-id="<?=$row['id']?>">
Rate
</button>
</td>

</tr>

<?php } ?>


<nav>
<ul class="pagination justify-content-end">

<?php for($i=1; $i<=$totalPages; $i++){ ?>

<li class="page-item <?=($i==$page)?'active':''?>">
<a href="javascript:void(0)"
class="page-link page-link-custom"
data-page="<?=$i?>">
<?=$i?>
</a>
</li>

<?php } ?>

</ul>
</nav>