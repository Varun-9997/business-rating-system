<?php
require "../conn.php";

$stmt = $conn->query("
SELECT 
r.name,
r.email,
r.phone,
r.rating,
b.name AS business_name
FROM ratings r
INNER JOIN businesses b ON b.id = r.business_id
ORDER BY r.id DESC
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$rows){
    echo "<p>No ratings found.</p>";
    exit;
}
?>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>#</th>
<th>Business</th>
<th>User</th>
<th>Email</th>
<th>Phone</th>
<th>Rating</th>
</tr>
</thead>

<tbody>

<?php $i=1; foreach($rows as $row){ ?>

<tr>
<td><?=$i++?></td>
<td><?=htmlspecialchars($row['business_name'])?></td>
<td><?=htmlspecialchars($row['name'])?></td>
<td><?=htmlspecialchars($row['email'])?></td>
<td><?=htmlspecialchars($row['phone'])?></td>
<td><?=$row['rating']?> / 5</td>
</tr>

<?php } ?>

</tbody>
</table>