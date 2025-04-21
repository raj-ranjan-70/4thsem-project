<?php
session_start();

// 🕒 Session timeout code (5 minutes)
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

// Load meals from database if they exist
$meals = [];
$host = "localhost";
$user = "root";
$password = "";
$dbname = "meal_planner";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT day, type, meal FROM meals WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $meals[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Weekly Meal Plan</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
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
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }

    .weekly {
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      padding: 30px;
      margin-bottom: 30px;
      transition: var(--transition);
    }

    .weekly-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .weekly-header h2 {
      font-size: 2.2rem;
      margin-bottom: 10px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
    }

    .weekly-header h2::after {
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

    .days-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .day-card {
      background-color: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      padding: 20px;
      transition: var(--transition);
    }

    .day-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .day-title {
      font-size: 1.4rem;
      color: var(--primary-color);
      margin-bottom: 15px;
      text-align: center;
      padding-bottom: 10px;
      border-bottom: 2px dashed #e9ecef;
    }

    .meal-time {
      margin-bottom: 15px;
    }

    .meal-time-title {
      font-size: 1rem;
      color: var(--dark-color);
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .meal-time-title span.material-symbols-outlined {
      font-size: 1.2rem;
      color: var(--accent-color);
    }

    .meal-content {
      min-height: 80px;
      border: 1px dashed #ced4da;
      border-radius: 8px;
      padding: 12px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      transition: var(--transition);
    }

    .meal-content:hover {
      border-color: var(--primary-color);
    }

    .meal-display {
      font-weight: 500;
      color: var(--dark-color);
      word-break: break-word;
    }

    .add-meal-btn {
      width: 100%;
      padding: 8px 12px;
      background-color: var(--light-color);
      border: 1px dashed #adb5bd;
      border-radius: 8px;
      color: var(--dark-color);
      font-size: 0.9rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: var(--transition);
    }

    .add-meal-btn:hover {
      background-color: #e9ecef;
      border-color: var(--primary-color);
      color: var(--primary-color);
    }

    .add-meal-btn span.material-symbols-outlined {
      font-size: 1.1rem;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 10px 20px;
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
    }

    .btn-secondary {
      background-color: var(--secondary-color);
      color: white;
    }

    .btn-secondary:hover {
      background-color: #3a9ae8;
      transform: translateY(-2px);
    }

    .btn-accent {
      background-color: var(--accent-color);
      color: white;
    }

    .btn-accent:hover {
      background-color: #e85959;
      transform: translateY(-2px);
    }

    .dropdown-list {
      list-style-type: none;
      padding: 8px 0;
      margin: 5px 0 0 0;
      background-color: white;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      max-height: 200px;
      overflow-y: auto;
      position: absolute;
      z-index: 1000;
      width: calc(100% - 24px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-list li {
      padding: 10px 15px;
      cursor: pointer;
      transition: var(--transition);
      font-size: 0.9rem;
    }

    .dropdown-list li:hover {
      background-color: #f1f3f5;
      color: var(--primary-color);
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
      .days-container {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      }
    }

    @media (max-width: 992px) {
      .weekly {
        padding: 20px;
      }
      
      .weekly-header h2 {
        font-size: 1.8rem;
      }
    }

    @media (max-width: 768px) {
      body {
        padding: 10px;
      }
      
      .container {
        padding: 10px;
      }
      
      .days-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
      }
      
      .day-card {
        padding: 15px;
      }
      
      .action-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .btn {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 576px) {
      .days-container {
        grid-template-columns: 1fr;
      }
      
      .weekly-header h2 {
        font-size: 1.5rem;
      }
    }

    /* Animation for meal cards */
    @keyframes slideIn {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .day-card {
      animation: slideIn 0.3s ease-out forwards;
      opacity: 0;
    }

    .day-card:nth-child(1) { animation-delay: 0.1s; }
    .day-card:nth-child(2) { animation-delay: 0.2s; }
    .day-card:nth-child(3) { animation-delay: 0.3s; }
    .day-card:nth-child(4) { animation-delay: 0.4s; }
    .day-card:nth-child(5) { animation-delay: 0.5s; }
    .day-card:nth-child(6) { animation-delay: 0.6s; }
    .day-card:nth-child(7) { animation-delay: 0.7s; }
  </style>
</head>
<body>
  <div class="container">
    <form action="meal_save.php" method="post" id="meal_form">
      <div class="weekly">
        <div class="weekly-header">
          <h2>Weekly Meal Plan</h2>
          <p>Plan your meals for the week and stay organized!</p>
        </div>
        
        <div class="days-container">
          <?php
          $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
          foreach ($days as $day) {
            echo '<div class="day-card">';
            echo '<h3 class="day-title">'.$day.'</h3>';
            
            $mealTypes = [
              'breakfast' => ['icon' => 'breakfast_dining', 'label' => 'Breakfast'],
              'lunch' => ['icon' => 'lunch_dining', 'label' => 'Lunch'],
              'dinner' => ['icon' => 'dinner_dining', 'label' => 'Dinner']
            ];
            
            foreach ($mealTypes as $type => $mealData) {
              echo '<div class="meal-time">';
              echo '<div class="meal-time-title">';
              echo '<span class="material-symbols-outlined">'.$mealData['icon'].'</span>';
              echo '<span>'.$mealData['label'].'</span>';
              echo '</div>';
              
              echo '<div class="meal-content '.$type.'">';
              
              $mealText = '';
              foreach ($meals as $meal) {
                if ($meal['day'] == $day && strtolower($meal['type']) == $type) {
                  $mealText = $meal['meal'];
                  break;
                }
              }
              
              if ($mealText) {
                echo '<div class="meal-display">'.$mealText.'</div>';
                echo '<input type="hidden" name="day[]" value="'.$day.'">';
                echo '<input type="hidden" name="type[]" value="'.ucfirst($type).'">';
                echo '<input type="hidden" name="meal[]" value="'.$mealText.'">';
              } else {
                echo '<button type="button" class="add-meal-btn">';
                echo '<span class="material-symbols-outlined">add</span>';
                echo 'Add '.$mealData['label'];
                echo '</button>';
              }
              
              echo '</div>'; // .meal-content
              echo '</div>'; // .meal-time
            }
            
            echo '</div>'; // .day-card
          }
          ?>
        </div>
        
        <div class="action-buttons">
          <button type="submit" name="save" class="btn btn-primary">
            <span class="material-symbols-outlined">save</span>
            Save Plan
          </button>
          <button type="button" name="edit" id="editBtn" class="btn btn-secondary">
            <span class="material-symbols-outlined">edit</span>
            Edit Plan
          </button>
          <button type="button" name="generate" class="btn btn-accent" formaction=>
            <span class="material-symbols-outlined">shopping_cart</span>
            Generate Grocery List
          </button>
        </div>
      </div>
    </form>
  </div>

  <script>
    const foodOptions = [
      "Omelette", "Pancakes", "Avocado Toast", "Yogurt Parfait", 
      "Salad", "Rice Bowl", "Chicken Curry", "Pasta", 
      "Fruits", "Sandwich", "Soup", "Stir Fry",
      "Grilled Salmon", "Vegetable Lasagna", "Burger", "Tacos",
      "Sushi", "Pizza", "Steak", "Risotto"
    ];

    function renderDropdown(parent) {
      const existing = parent.querySelector('.dropdown-list');
      if (existing) {
        existing.remove();
        return;
      }

      const dropdown = document.createElement("ul");
      dropdown.className = "dropdown-list";

      foodOptions.forEach(food => {
        const item = document.createElement("li");
        item.innerText = food;
        item.addEventListener("click", () => {
          dropdown.remove();

          const addBtn = parent.querySelector(".add-meal-btn");
          if (addBtn) addBtn.style.display = "none";

          let display = parent.querySelector(".meal-display");
          if (!display) {
            display = document.createElement("div");
            display.className = "meal-display";
            parent.insertBefore(display, parent.firstChild);
          }

          display.innerText = food;

          const day = parent.closest(".day-card").querySelector(".day-title").innerText;
          const type = parent.classList.contains("breakfast") ? "Breakfast" :
                       parent.classList.contains("lunch") ? "Lunch" : "Dinner";

          // Remove any existing hidden inputs for this meal time
          parent.querySelectorAll('input[type="hidden"]').forEach(input => input.remove());

          const inputs = [
            { name: 'day[]', value: day },
            { name: 'type[]', value: type },
            { name: 'meal[]', value: food }
          ];

          inputs.forEach(inputData => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = inputData.name;
            input.value = inputData.value;
            parent.appendChild(input);
          });
        });
        dropdown.appendChild(item);
      });

      parent.appendChild(dropdown);

      document.addEventListener("click", function handler(e) {
        if (!parent.contains(e.target)) {
          dropdown.remove();
          document.removeEventListener("click", handler);
        }
      });
    }

    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll(".add-meal-btn").forEach(button => {
        button.addEventListener("click", function() {
          renderDropdown(this.parentElement);
        });
      });

      document.getElementById("editBtn").addEventListener("click", () => {
        const confirmed = confirm("Are you sure you want to edit all meals? This will clear your current selections.");
        if (!confirmed) return;
        
        document.querySelectorAll(".meal-content").forEach(mealContent => {
          const display = mealContent.querySelector(".meal-display");
          if (display) {
            display.remove();
            mealContent.querySelectorAll('input[type="hidden"]').forEach(input => input.remove());

            const addBtn = document.createElement("button");
            addBtn.type = "button";
            addBtn.className = "add-meal-btn";
            
            const icon = document.createElement("span");
            icon.className = "material-symbols-outlined";
            icon.innerText = "add";
            
            const text = document.createTextNode("Add ");
            
            // Get the meal type from the parent container
            const mealType = mealContent.classList.contains("breakfast") ? "Breakfast" :
                            mealContent.classList.contains("lunch") ? "Lunch" : "Dinner";
            
            const typeText = document.createTextNode(mealType);
            
            addBtn.appendChild(icon);
            addBtn.appendChild(text);
            addBtn.appendChild(typeText);
            
            addBtn.addEventListener("click", function() {
              renderDropdown(mealContent);
            });
            
            mealContent.appendChild(addBtn);
          }
        });
      });

      document.querySelector('button[name="generate"]').addEventListener("click", function() {
          // Collect all meal data from hidden inputs
          const mealData = [];
          document.querySelectorAll('input[name="meal[]"]').forEach(input => {
              mealData.push(input.value);
          });
          
          if (mealData.length === 0) {
              alert("Please add some meals to your plan before generating a grocery list.");
              return;
          }
          
          // Send data to server to process and redirect to grocery cart
          const form = document.getElementById('meal_form');
          form.action = 'generate_cart.php'; // New PHP file to process the data
          form.submit();
      });
    });
  </script>
</body>
</html>