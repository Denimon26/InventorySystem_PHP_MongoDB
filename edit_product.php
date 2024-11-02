<?php
  $page_title = 'Edit product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
   use MongoDB\Client;

   function page_require_level($required_level)
{
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $admins = $database->selectCollection('admin');
  $admin = $admins->findOne(['_id' => $_SESSION['user_id']]);

  if (!isset($admin)) {

    redirect('index.php', false);
  }
  if ($admin['user_level'] <= (int) $required_level) {
    return true;
  } else {
    // If the user does not have permission, redirect to the home page
    redirect('home.php', false);
  }
}


?>
<?php

$uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$client = new Client($uri);
$database = $client->selectDatabase('inventory_system');

$prod = $database->selectCollection('product');
$cats = $database->selectCollection('categories');
$media = $database->selectCollection('group');

$prodid = $_GET['id'];

$product =  $prod->findOne(['name' => $prodid]);
$all_categories = $cats->find();
$all_photo = $media->find();

?>

<?php
if (isset($_POST['product'])) {
  $req_fields = array('product-title', 'product-categorie', 'product-quantity', 'buying-price');
  validate_fields($req_fields);

  if (empty($errors)) {
      $p_name  = remove_junk(($_POST['product-title']));
      $p_cat   = $_POST['product-categorie'];
      $p_qty   = remove_junk(($_POST['product-quantity']));
      $p_buy   = remove_junk(($_POST['buying-price']));

      // Calculate critical level (this is just an example, you can adjust this formula)
      $p_critical = 20;

      if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
          $media_id = '0';
      } else {
          $media_id = remove_junk(($_POST['product-photo']));
      }

      $data = [
        'name' => $p_name,
        'categories' => $p_cat,
        'buy_price' => $p_buy,
        'quantity' => $p_qty,
        'date' =>  new MongoDB\BSON\UTCDateTime(),
        'image' => "",
        'media_id' => $media_id
  
      ];
      $result = $prod->updateOne(
        ['name' => $_GET['id']],
        ['$set' => $data]
      );
      echo "<script>console.log('".json_encode($result)."');</script>";

      if ($result->getModifiedCount() > 0) {
        //sucess
        $session->msg('s', "Product has been updated! ");
        echo "<script>console.log('done');</script>";
        redirect('product.php?id=' . (int) $e_group['id'], false);
      } else {
        //failed
        $session->msg('d', ' Sorry failed to updated Product!');
        echo "<script>console.log('f');</script>";
  
        redirect('edit_product.php?id=' .  $p_name, false);
        
      }
  } else {
      $session->msg("d", $errors);
      redirect('edit_product.php?id=' . $p_name, false);
  }
}
?>

<?php //include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-12">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-7">
           <form method="post" action="edit_product.php?id=<?php echo $product['name'] ?>">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" value="<?php echo remove_junk($product['name']);?>">
               </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                    <option value=""> Select a categorie</option>
                   <?php  foreach ($all_categories as $cat): ?>
                     <option value="<?php echo $cat['name']; ?>" <?php if($product['categories'] === $cat['name']): echo $cat['name']." (selected)"; endif; ?> >
                       <?php echo remove_junk($cat['name']); ?></option>
                   <?php endforeach; ?>
                 </select>
                  </div>
                  <div class="col-md-6">
                    <select class="form-control" name="product-photo">
                      <option value=""> No image</option>
                        <option value="T1"  >T1</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Quantity</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                       <i class="glyphicon glyphicon-shopping-cart"></i>
                      </span>
                      <input type="number" class="form-control" name="product-quantity" value="<?php echo remove_junk($product['quantity']); ?>">
                   </div>
                  </div>
                 </div>
                 <div class="col-md-4">
                  <div class="form-group">
                    <label for="qty">Buying price</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="glyphicon glyphicon-usd"></i>
                      </span>
                      <input type="number" class="form-control" name="buying-price" value="<?php echo remove_junk($product['buy_price']);?>">
                      <span class="input-group-addon">.00</span>
                   </div>
                  </div>
                 </div>
               </div>
              </div>
              <button type="submit" name="product" class="btn btn-danger">Update</button>
          </form>
         </div>
        </div>
      </div>
  </div>

<?php //include_once('layouts/footer.php'); ?>