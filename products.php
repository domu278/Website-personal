<?php 
$title = "";
$sub_title = "";
if(isset($_GET['c']) && isset($_GET['s'])){
    $cat_qry = $conn->query("SELECT * FROM categories where md5(id) = '{$_GET['c']}'");
    if($cat_qry->num_rows > 0){
        $result =$cat_qry->fetch_assoc();
        $title = $result['category'];
        $cat_description = $result['description'];
    }
 $sub_cat_qry = $conn->query("SELECT * FROM sub_categories where md5(id) = '{$_GET['s']}'");
    if($sub_cat_qry->num_rows > 0){
        $result =$sub_cat_qry->fetch_assoc();
        $sub_title = $result['sub_category'];
        $sub_cat_description = $result['description'];
    }
}
elseif(isset($_GET['c'])){
    $cat_qry = $conn->query("SELECT * FROM categories where md5(id) = '{$_GET['c']}'");
    if($cat_qry->num_rows > 0){
        $result =$cat_qry->fetch_assoc();
        $title = $result['category'];
        $cat_description = $result['description'];
    }
}
elseif(isset($_GET['s'])){
    $sub_cat_qry = $conn->query("SELECT * FROM sub_categories where md5(id) = '{$_GET['s']}'");
    if($sub_cat_qry->num_rows > 0){
        $result =$sub_cat_qry->fetch_assoc();
        $sub_title = $result['sub_category'];
        $sub_cat_description = $result['description'];
    }
}
?>
<!-- Header-->
 <!-- Header-->
 <header class=" py-5" id="main-header">
</header>
<!-- Section-->
<section class="py-5">
    <div class="container-fluid row">
        <?php if(isset($_GET['c'])): ?>
        <div class="col-md-3 border-right mb-2 pb-3">
            <h3><b>Sub Categories</b></h3>
            <div class="list-group">
                <a href="./?p=products&c=<?php echo $_GET['c'] ?>" class="list-group-item  text-light <?php echo !isset($_GET['s']) ? "active" : "" ?>">All</a>
                <?php 
                $sub_cat = $conn->query("SELECT * FROM `sub_categories` where md5(parent_id) =  '{$_GET['c']}' ");
                while($row = $sub_cat->fetch_assoc()):
                ?>
                    <a href="./?p=products&c=<?php echo $_GET['c'] ?>&s=<?php echo md5($row['id']) ?>" class="list-group-item  text-light  <?php echo isset($_GET['s']) && $_GET['s'] == md5($row['id']) ? "active" : "" ?>"><?php echo $row['sub_category'] ?></a>
                <?php endwhile; ?>
            </div>
            <hr>
        <div class="content">
            <p class="text-center"><b><?php echo $title. " Category" ?></b></p>
            <hr class="border-primary">
            <div>
                <?php echo isset($cat_description) ? stripslashes(html_entity_decode($cat_description)) : '' ?>
            </div>
            <?php if(isset($sub_cat_description)): ?>
            <p class="text-center"><b><?php echo $sub_title?></b></p>
            <hr>
            <div>
                <?php echo isset($sub_cat_description) ? stripslashes(html_entity_decode($sub_cat_description)) : '' ?>
            </div>
            <?php endif; ?>
        </div>
        </div>
        <?php endif; ?>
        <div class="<?php echo isset($_GET['c'])? 'col-md-9': 'col-md-10 offset-md-1' ?>">
            <div class="container-fluid p-0">
            <?php 
                            if(isset($_GET['search'])){
                                echo "<h4 class='text-center'><b>Search Result for '".$_GET['search']."'</b></h4><hr>";
                            }
                        ?>
                    
                    <div class="row gx-2 gx-lg-2 row-cols-1 row-cols-md-3 row-cols-lg-4 <?php echo (isset($_GET['search'])) ? 'justify-content-center':'' ?> ">
                    
                        <?php 
                            $whereData = "";
                            if(isset($_GET['search']))
                                $whereData = " and (title LIKE '%{$_GET['search']}%' or description LIKE '%{$_GET['search']}%')";
                            elseif(isset($_GET['c']) && isset($_GET['s']))
                                $whereData = " and (md5(category_id) = '{$_GET['c']}' and md5(sub_category_id) = '{$_GET['s']}')";
                            elseif(isset($_GET['c']) && !isset($_GET['s']))
                                $whereData = " and md5(category_id) = '{$_GET['c']}' ";
                            elseif(isset($_GET['s']) && !isset($_GET['c']))
                                $whereData = " and md5(sub_category_id) = '{$_GET['s']}' ";
                            $products = $conn->query("SELECT * FROM `products` where status = 1 {$whereData} order by rand() ");
                            while($row = $products->fetch_assoc()):
                                $upload_path = base_app.'/uploads/product_'.$row['id'];
                                $img = "";
                                if(is_dir($upload_path)){
                                    $fileO = scandir($upload_path);
                                    if(isset($fileO[2]))
                                        $img = "uploads/product_".$row['id']."/".$fileO[2];
                                    // var_dump($fileO);
                                }
                                foreach($row as $k=> $v){
                                    $row[$k] = trim(stripslashes($v));
                                }
                                $inventory = $conn->query("SELECT * FROM inventory where product_id = ".$row['id']);
                                $inv = array();
                                while($ir = $inventory->fetch_assoc()){
                                    $inv[] = number_format($ir['price']);
                                }
                    $row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
                    ?>
                        <div class="col-md-12 mb-5">
                            <div class="card product-item">
                                <!-- Product image-->
                                <img class="card-img-top w-100" src="<?php echo validate_image($img) ?>" loading="lazy" alt="..." style=""/>
                                <!-- Product details-->
                                <div class="card-body p-2">
                                    <div class="">
                                        <!-- Product name-->
                                        <h5 class="fw-bolder"><?php echo $row['title'] ?></h5>
                                        <!-- Product price-->
                                        <?php foreach($inv as $k=> $v): ?>
                                            <span><b>Price: </b><?php echo $v ?></span>
                                        <?php endforeach; ?>
                                    <small class="truncate border-top"><?php echo $row['description'] ?></small>
                                    </div>
                                </div>
                                <!-- Product actions-->
                                <div class="card-footer p-2 pt-0 border-top-0 bg-transparent">
                                    <div class="text-center">
                                        <a class="btn btn-flat btn-primary "   href=".?p=view_product&id=<?php echo md5($row['id']) ?>">View</a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php 
                            if($products->num_rows <= 0){
                                echo "<h4 class='text-center'><b>No Product Listed.</b></h4>";
                            }
                        ?>
                    </div>
             </div>   
            

        </div>
    </div>
</section>