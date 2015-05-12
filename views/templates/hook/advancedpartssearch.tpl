<!-- Block mymodule -->
<div id="mymodule_block_home" class="block col-lg-12 clearfix">
  <!-- <h4>Welcome!</h4> -->
  <div class="block_content">
    <!-- <p>Hello,
       {if isset($my_module_name) && $my_module_name}
           {$my_module_name}
       {else}
           World
       {/if}
       !       
    </p> 
    <ul>
      <li><a href="{$my_module_link}" title="Click this link">Click me!</a></li>
    </ul> -->
    <form class="form-inline">
      <div class="form-group">
        <label for="category">Category</label>
        <select id="category" class="form-control" onchange="getBrands(this)">
          <option>-- Select Category --</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="brand">Brand</label>
        <select id="brand" class="form-control" onchange="getSeries(this)">
          <option>-- Select Brand --</option>
        </select>
      </div>

      <div class="form-group">
        <label for="series">Series</label>
        <select id="series" class="form-control" onchange="getModels(this)">
          <option>-- Select Series --</option>
        </select>
      </div>

      <div class="form-group">
        <label for="model">Model</label>
        <select id="model" class="form-control">
          <option>-- Select Model --</option>
        </select>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-default">Search</button>
      </div>
    </form>  
    
  </div>
</div>
<!-- /Block mymodule -->

<!-- Module jQuery -->
<script>

function getData(params)
{
  // var baseDir = 'http://localhost/prestashop16/';
  // $('#brand').empty()
  
  $.ajax({    
    type: "POST",
    url: '{$base_url}modules/advancedpartssearch/ajax.php',
    data: params, //{ 'method': 'getCategories' },
    success: function(data){

      var opts = $.parseJSON(data);
      var id = name = null;

      for (var j = 0; j < opts.length; j++) {
        $.each(opts[j], function(i, d) {                
          if(i == 'Id') id = d;
          if(i == 'Name') name = d;
        });

        if(id != null && name != null) {
          $(params.node).append('<option value="' + id + '">' + name + '</option>');
          id = name = null;
        }
      }
    }
  });
}

getCategories();

function getCategories() {
  var selector = '#category';
  $(selector).empty();
  // var dropDown = document.getElementById("category");
  // var category = dropDown.options[dropDown.selectedIndex].value;
  var params = { node: selector, 'method': 'getCategories' };
  getData(params);
}

function getBrands(obj) {
  var selector = '#brand';
  $(selector).empty();
  var dropDown = document.getElementById("category");
  var category = dropDown.options[dropDown.selectedIndex].value;
  var params = { node: selector, 'category': category, 'method': 'getBrands' };
  getData(params);
}

function getSeries(obj) {
  var selector = '#series';
  $(selector).empty();
  var dropDown = document.getElementById("brand");
  var brand = dropDown.options[dropDown.selectedIndex].value;
  var params = { node: selector, 'brand': brand, 'method': 'getSeries' };
  getData(params);
}

function getModels(obj) {
  var selector = '#model';
  $(selector).empty();
  var dropDown = document.getElementById("series");
  var series = dropDown.options[dropDown.selectedIndex].value;
  var params = { node: selector, 'series': series, 'method': 'getModels' };
  getData(params);
}

// function getBrands1(obj)
// {
//   var baseDir = 'http://localhost/prestashop16/';
//   $('#brand').empty();
//   var dropDown = document.getElementById("category");
//   var category = dropDown.options[dropDown.selectedIndex].value;

//   $.ajax({    
//     type: "POST",
//     url: 'http://localhost/prestashop16/modules/advancedpartssearch/ajax.php',
//     data: { 'category': category, 'method': 'getBrands' },
//     success: function(data){

//       var opts = $.parseJSON(data);
//       var brandid = brandname = null;

//       for (var j = opts.length - 1; j >= 0; j--) {
//         $.each(opts[j], function(i, d) {                
//           if(i == 'BrandId') brandid = d;
//           if(i == 'BrandName') brandname = d;
//         });

//         if(brandid != null && brandname != null) {
//           $('#brand').append('<option value="' + brandid + '">' + brandname + '</option>');
//           brandid = brandname = null;
//         }
//       }
//     }
//   });
// }

</script>