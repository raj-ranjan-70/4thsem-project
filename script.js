function searchRecipes() {
    const query = document.getElementById("mainSearch").value.trim();
    if (query) {
      document.location.href = "./recipie_search/dashboard.html";
    } else {
      alert("Please enter something to search!");
    }
  }

  function redirectToMealPlanner() {
    fetch("check_login.php")
      .then(response => {
        console.log("Raw response:", response);
        return response.text(); // First get as text to see what's returned
      })
      .then(text => {
        console.log("Response text:", text);
        try {
          const data = JSON.parse(text);
          console.log("Parsed data:", data);
          if (data.status === 'logged_in') {
            window.location.href="./Weeklyplan/index.php";
          } else {
            window.location.href="./Login/login.html";
          }
        } catch (e) {
          console.error("JSON parse error:", e);
          window.location.href="./Login/login.html";
        }
      })
      .catch(error => {
        console.error("Fetch error:", error);
        window.location.href="./Login/login.html";
      });
  }

  document.getElementById("accountBtn").addEventListener("click", () => {
    document.getElementById("accountMenu").classList.toggle("show-menu");
  });

  document.addEventListener("DOMContentLoaded", () => {
    fetch("check_login.php")
      .then(response => response.json())
      .then(data => {
        const logoutLink = document.getElementById("logoutLink");
        const loginLink = document.getElementById("loginLink");
        const signupLink = document.getElementById("signupLink");
        const userGreeting = document.getElementById("userGreeting");

        if (data.status === "logged_in") {
          logoutLink.style.display = "block";
          loginLink.style.display = "none";
          signupLink.style.display = "none";
          userGreeting.style.display = "block";
          userGreeting.textContent = `👋 ${data.username}`;
        } else {
          logoutLink.style.display = "none";
          loginLink.style.display = "block";
          signupLink.style.display = "block";
          userGreeting.style.display = "none";
        }
      })
      .catch(error => {
        console.error("Login check failed:", error);
      });
  });
  const marquee = document.getElementById("reviewMarquee");
  const cards = marquee.querySelectorAll(".review-card");

  cards.forEach(card => {
    card.addEventListener("mouseenter", () => {
      marquee.classList.add("paused");
      cards.forEach(c => {
        if (c !== card) c.style.filter = "blur(3px)";
      });
    });

    card.addEventListener("mouseleave", () => {
      marquee.classList.remove("paused");
      cards.forEach(c => c.style.filter = "none");
    });

    card.addEventListener("click", () => {
      marquee.classList.add("paused");
      cards.forEach(c => {
        if (c !== card) c.style.filter = "blur(3px)";
      });
    });
  });