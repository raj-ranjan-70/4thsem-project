<?php
session_start();

// Session timeout (5 minutes)
$timeout_duration = 300;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../Login/login.html?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: ../Login/login.html");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "meal_planner";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all meals for the user
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT day, type, meal FROM meals WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$meals = [];
while ($row = $result->fetch_assoc()) {
    $meals[] = $row;
}
$stmt->close();

// Process meals into grocery items
$groceryItems = [];
$ingredientMap = [
    "Omelette" => ["Eggs", "Cheese", "Milk", "Vegetables"],
    "Pancakes" => ["Flour", "Eggs", "Milk", "Butter", "Baking Powder"],
    "Salad" => ["Lettuce", "Tomatoes", "Cucumber", "Olive Oil", "Vinegar"],
    "Rice" => ["Rice", "Butter", "Salt"],
    "Chicken Curry" => ["Chicken", "Curry Powder", "Coconut Milk", "Onions", "Garlic"],
    "Pasta" => ["Pasta", "Tomato Sauce", "Cheese", "Basil"],
    "Fruits" => ["Assorted Fruits"],
    "Avocado Toast" => ["Bread", "Avocado", "Salt", "Pepper"],
    "Yogurt Parfait" => ["Yogurt", "Granola", "Berries"],
    "Sandwich" => ["Bread", "Cheese", "Ham", "Lettuce", "Mayonnaise"],
    "Soup" => ["Vegetables", "Stock", "Herbs"],
    "Stir Fry" => ["Rice Noodles", "Vegetables", "Soy Sauce", "Chicken"],
    "Grilled Salmon" => ["Salmon", "Lemon", "Dill", "Butter"],
    "Vegetable Lasagna" => ["Lasagna Sheets", "Tomato Sauce", "Cheese", "Vegetables"],
    "Burger" => ["Buns", "Beef Patty", "Cheese", "Lettuce", "Tomato"],
    "Tacos" => ["Taco Shells", "Ground Beef", "Lettuce", "Cheese", "Salsa"],
    "Sushi" => ["Rice", "Nori", "Fish", "Vegetables"],
    "Pizza" => ["Pizza Dough", "Tomato Sauce", "Cheese", "Toppings"],
    "Steak" => ["Beef Steak", "Butter", "Herbs"],
    "Risotto" => ["Arborio Rice", "Stock", "Parmesan", "Mushrooms"]
];

foreach ($meals as $meal) {
    if (isset($ingredientMap[$meal['meal']])) {
        foreach ($ingredientMap[$meal['meal']] as $ingredient) {
            if (!isset($groceryItems[$ingredient])) {
                $groceryItems[$ingredient] = 1;
            } else {
                $groceryItems[$ingredient]++;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grocery Cart - Meal Planner</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
  <style>
    :root {
      --primary-color: #6c63ff;
      --secondary-color: #4dabf7;
      --accent-color: #ff6b6b;
      --light-color: #f8f9fa;
      --dark-color: #343a40;
      --success-color: #51cf66;
      --warning-color: #fcc419;
      --border-radius: 12px;
      --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: var(--dark-color);
      line-height: 1.6;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
    }

    .header h1::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      border-radius: 2px;
    }

    .header p {
      color: #6c757d;
      font-size: 1.1rem;
    }

    .cart-container {
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      padding: 30px;
      margin-bottom: 30px;
    }

    .cart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #e9ecef;
    }

    .cart-title {
      font-size: 1.5rem;
      color: var(--primary-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .cart-title span.material-symbols-outlined {
      font-size: 1.8rem;
    }

    .cart-summary {
      background-color: #f8f9fa;
      border-radius: var(--border-radius);
      padding: 20px;
      margin-bottom: 30px;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .summary-total {
      font-weight: 600;
      font-size: 1.1rem;
      border-top: 1px solid #e9ecef;
      padding-top: 10px;
      margin-top: 10px;
    }

    .grocery-list {
      list-style-type: none;
    }

    .grocery-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px;
      border-bottom: 1px solid #e9ecef;
      transition: var(--transition);
    }

    .grocery-item:hover {
      background-color: #f8f9fa;
    }

    .item-info {
      display: flex;
      align-items: center;
      gap: 15px;
      flex: 1;
    }

    .item-checkbox {
      width: 20px;
      height: 20px;
      cursor: pointer;
    }

    .item-name {
      font-weight: 500;
      flex: 1;
    }

    .item-name.checked {
      text-decoration: line-through;
      color: #6c757d;
    }

    .item-quantity {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .quantity-btn {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: 1px solid #ced4da;
      background-color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }

    .quantity-btn:hover {
      background-color: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
    }

    .item-actions {
      display: flex;
      gap: 10px;
      margin-left: 20px;
    }

    .action-btn {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      border: none;
      background-color: #f1f3f5;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }

    .action-btn:hover {
      background-color: var(--accent-color);
      color: white;
    }

    .empty-cart {
      text-align: center;
      padding: 40px 0;
      color: #6c757d;
    }

    .empty-cart span.material-symbols-outlined {
      font-size: 3rem;
      margin-bottom: 20px;
      color: #adb5bd;
    }

    .action-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 15px;
      margin-top: 30px;
    }

    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: var(--border-radius);
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background-color: var(--primary-color);
      color: white;
    }

    .btn-primary:hover {
      background-color: #5a51e6;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(108, 99, 255, 0.3);
    }

    .btn-outline {
      background-color: white;
      color: var(--primary-color);
      border: 1px solid var(--primary-color);
    }

    .btn-outline:hover {
      background-color: #f8f9fa;
      transform: translateY(-2px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .container {
        padding: 10px;
      }
      
      .header h1 {
        font-size: 2rem;
      }
      
      .cart-container {
        padding: 20px;
      }
      
      .item-info {
        gap: 10px;
      }
      
      .item-actions {
        margin-left: 10px;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 576px) {
      .grocery-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .item-quantity {
        width: 100%;
        justify-content: space-between;
      }
      
      .item-actions {
        width: 100%;
        justify-content: flex-end;
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Grocery Cart</h1>
      <p>All the ingredients you need for your meal plan</p>
    </div>
    
    <div class="cart-container">
      <div class="cart-header">
        <div class="cart-title">
          <span class="material-symbols-outlined">shopping_cart</span>
          <span>My Grocery List</span>
        </div>
        <div class="cart-summary">
          <div class="summary-item">
            <span>Total Items:</span>
            <span><?php echo count($groceryItems); ?></span>
          </div>
          <div class="summary-item summary-total">
            <span>Estimated Cost:</span>
            <span>Rs.<?php echo number_format(count($groceryItems) * 3.5 * 80, 2); ?></span>
          </div>
        </div>
      </div>
      
      <?php if (empty($groceryItems)): ?>
        <div class="empty-cart">
          <span class="material-symbols-outlined">shopping_basket</span>
          <h3>Your grocery cart is empty</h3>
          <p>Add meals to your weekly plan to generate a grocery list</p>
          <a href="weekly_meal_plan.php" class="btn btn-primary" style="margin-top: 20px;">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Meal Planner
          </a>
        </div>
      <?php else: ?>
        <ul class="grocery-list">
          <?php foreach ($groceryItems as $item => $quantity): ?>
            <li class="grocery-item" data-item="<?php echo htmlspecialchars($item); ?>">
              <div class="item-info">
                <input type="checkbox" class="item-checkbox" id="<?php echo 'item-' . preg_replace('/\s+/', '-', strtolower($item)); ?>">
                <label for="<?php echo 'item-' . preg_replace('/\s+/', '-', strtolower($item)); ?>" class="item-name"><?php echo htmlspecialchars($item); ?></label>
              </div>
              <div class="item-quantity">
                <button class="quantity-btn minus">-</button>
                <span class="quantity"><?php echo $quantity; ?></span>
                <button class="quantity-btn plus">+</button>
              </div>
              <div class="item-actions">
                <button class="action-btn" title="Remove item">
                  <span class="material-symbols-outlined">delete</span>
                </button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        
        <div class="action-buttons">
          <button class="btn btn-outline">
            <span class="material-symbols-outlined">print</span>
            Print List
          </button>
          <button class="btn btn-primary" id="markAllComplete">
            <span class="material-symbols-outlined">check</span>
            Mark All Complete
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Toggle item checked state
      document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
          const itemName = this.nextElementSibling;
          if (this.checked) {
            itemName.classList.add('checked');
          } else {
            itemName.classList.remove('checked');
          }
        });
      });

      // Quantity buttons
      document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const isPlus = this.classList.contains('plus');
          const quantityElement = this.parentElement.querySelector('.quantity');
          let quantity = parseInt(quantityElement.textContent);
          
          if (isPlus) {
            quantity++;
          } else {
            if (quantity > 1) quantity--;
          }
          
          quantityElement.textContent = quantity;
        });
      });

      // Remove item
      document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const item = this.closest('.grocery-item');
          if (confirm('Remove this item from your grocery list?')) {
            item.style.opacity = '0';
            setTimeout(() => {
              item.remove();
              updateSummary();
            }, 300);
          }
        });
      });

      // Mark all complete
      document.getElementById('markAllComplete').addEventListener('click', function() {
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
          checkbox.checked = true;
          checkbox.nextElementSibling.classList.add('checked');
        });
      });

      // Print list
      document.querySelector('.btn-outline').addEventListener('click', function() {
        window.print();
      });

      // Update summary when items change
      function updateSummary() {
        const itemCount = document.querySelectorAll('.grocery-item').length;
        if (document.querySelector('.summary-item span:last-child')) {
          document.querySelector('.summary-item span:last-child').textContent = itemCount;
          document.querySelector('.summary-total span:last-child').textContent = 
            'Rs.' + (itemCount * 3.5 * 80).toFixed(2);
        }
      }
    });
  </script>
</body>
</html>