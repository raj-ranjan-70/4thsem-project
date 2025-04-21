// Enhanced JavaScript with dynamic search
const recipes = [
    {
        name: 'Vegetarian Pasta Primavera',
        image: 'https://images.unsplash.com/photo-1675092789086-4bd2b93ffc69?q=80&w=2889&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['pasta', 'zucchini', 'bell peppers', 'cherry tomatoes', 'olive oil'],
        time: '30 mins',
        calories: '450',
        tags: ['vegetarian', 'italian', 'quick-meal']
    },
    {
        name: 'Chicken Caesar Salad',
        image: 'https://images.unsplash.com/photo-1670237735381-ac5c7fa72c51?q=80&w=2906&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['chicken breast', 'romaine lettuce', 'croutons', 'parmesan', 'caesar dressing'],
        time: '20 mins',
        calories: '320',
        tags: ['low-carb', 'high-protein']
    },
    {
        name: 'Vegan Black Bean Tacos',
        image: 'https://images.unsplash.com/photo-1662743086910-38419bbf7f34?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['black beans', 'corn tortillas', 'avocado', 'salsa',
        'cumin', 'chili powder', 'lime juice'],
        time: '25 mins',
        calories: '400',
        tags: ['vegan', 'mexican', 'quick-meal']
    },
    {
        name: 'Vegetable Stir fry',
        image: 'https://images.unsplash.com/photo-1599297915779-0dadbd376d49?q=80&w=3132&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['broccoli', 'bell peppers', 'onion', 'soy sauce',
        'garlic', 'ginger', 'sesame oil'],
        time: '20 mins',
        calories: '300',
        tags: ['vegetarian', 'asian', 'quick-meal']
    },
    {
        name: 'Chicken Fajitas',
        image: 'https://images.unsplash.com/photo-1666025954339-97a97468de17?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['chicken breast', 'bell peppers', 'onion', 'fajita',
        'seasoning', 'tortillas', 'shredded cheese'],
        time: '25 mins',
        calories: '400',
        tags: ['low-carb', 'high-protein']
    },
    {
        name: 'Vegan Quinoa Bowl',
        image: 'https://images.unsplash.com/photo-1544535754-d429f7a0cf95?q=80&w=3072&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['quinoa', 'black beans', 'corn', 'avocado',
        'salsa', 'cumin', 'chili powder'],
        time: '25 mins',
        calories: '400',
        tags: ['vegan', 'mexican', 'quick-meal']
    },
    {
        name: 'One-Pan Coconut-Lime Chicken',
        image: 'https://plus.unsplash.com/premium_photo-1669245207563-6b10e22befc7?q=80&w=3071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['chicken breast', 'coconut milk', 'lime juice',
        'cumin', 'chili powder', 'garlic', 'ginger'],
        time: '25 mins',
        calories: '400',
        tags: ['low-carb', 'high-protein','gluten-free']
    },
    {
        name:'Cacio E Pepe Sweet Potato Noodles',
        image: 'https://images.unsplash.com/photo-1648003497161-d8317d2b7163?q=80&w=3174&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['sweet potato noodles', 'parmesan cheese',
        'black pepper', 'olive oil', 'garlic'],
        time: '20 mins',
        calories: '300',
        tags: ['low-carb', 'gluten-free']
    },
    {
        name:'Sahi Panner',
        image: 'https://images.pexels.com/photos/12737805/pexels-photo-12737805.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
        ingredients: ['paneer', 'garam masala', 'cumin powder', 'coriender','onion','garlic','chili powder','salt'],
        time:'25 mins',
        calories:'400',
        tags:['indian', 'low-carb', 'high-protein']
    },
    {
        name:'Ghevar',
        image: 'https://www.shutterstock.com/shutterstock/photos/2169885085/display_1500/stock-photo-indian-rajasthani-crunchy-sweet-dish-called-ghevar-or-ghewar-is-an-made-using-refined-flour-2169885085.jpg',
        ingredients: ['all-purpose flour', 'ghee', 'milk', 'sugar',
            'cardamom', 'chopped nuts'],
        time: '30 mins',
        calories: '500',
        tags: ['indian', 'sweet']
    },
    {
        name:'Litti Chokha',
        image: 'https://images.unsplash.com/photo-1732899830100-e641144f8802?q=80&w=2960&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
        ingredients: ['litti', 'chokha', 'ghee', 'salt', 
            'chopped onion', 'chopped tomato'],
        time: '30 min',
        calories: '400',
        tags: ['indian', 'low-carb']
    }
];

const searchInput = document.getElementById('recipeSearch');
const recipeResults = document.getElementById('recipeResults');
const loadingSpinner = document.getElementById('loading');
const filterTags = document.querySelectorAll('.filter-tag');

// Real-time search with debounce
let searchTimeout;
searchInput.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    loadingSpinner.style.display = 'block';
    
    searchTimeout = setTimeout(() => {
        searchRecipes(e.target.value);
        loadingSpinner.style.display = 'none';
    }, 500);
});

// Filter tag click handler
filterTags.forEach(tag => {
    tag.addEventListener('click', function() {
        this.classList.toggle('active');
        searchRecipes(searchInput.value);
    });
});

function searchRecipes(query) {
    const activeFilters = Array.from(document.querySelectorAll('.filter-tag.active'))
                             .map(tag => tag.dataset.filter);

    const filteredRecipes = recipes.filter(recipe => {
        const matchesSearch = recipe.name.toLowerCase().includes(query.toLowerCase()) ||
            recipe.ingredients.some(ingredient => 
                ingredient.toLowerCase().includes(query.toLowerCase())
            );
        
        const matchesFilters = activeFilters.length === 0 || 
            activeFilters.some(filter => recipe.tags.includes(filter));
        
        return matchesSearch && matchesFilters;
    });

    displayRecipes(filteredRecipes);
}

function displayRecipes(recipes) {
    recipeResults.innerHTML = '';
    
    recipes.forEach(recipe => {
        const recipeCard = document.createElement('div');
        recipeCard.className = 'recipe-card';
        recipeCard.innerHTML = `
            <div class="recipe-image">
                <img src="${recipe.image}" alt="${recipe.name}">
            </div>
            <div class="recipe-content">
                <h3 class="recipe-title">${recipe.name}</h3>
                <div class="recipe-meta">
                    <span><i class="fas fa-clock"></i> ${recipe.time}</span>
                    <span><i class="fas fa-fire"></i> ${recipe.calories} kcal</span>
                </div>
                <div class="recipe-ingredients">
                    ${recipe.ingredients.map(ing => `
                        <span class="ingredient-tag">${ing}</span>
                    `).join('')}
                </div>
                <div class="recipe-actions">
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-bookmark"></i> Save
                    </button>
                    <button class="btn btn-sm btn-secondary">
                        <i class="fas fa-shopping-cart"></i> Add Ingredients
                    </button>
                </div>
            </div>
        `;
        
        recipeResults.appendChild(recipeCard);
    });
}

// Initial display of all recipes
displayRecipes(recipes);