// Toggle menu for mobile devices
function toggleMenu() {
  var MenuItems = document.getElementById("MenuItems")
  if (MenuItems.style.maxHeight == "0px" || !MenuItems.style.maxHeight) {
    MenuItems.style.maxHeight = "200px"
  } else {
    MenuItems.style.maxHeight = "0px"
  }
}

// Product quantity selector
document.addEventListener("DOMContentLoaded", () => {
  const quantityInput = document.getElementById("quantity")

  if (quantityInput) {
    // Ensure quantity is always at least 1
    quantityInput.addEventListener("change", function () {
      if (this.value < 1) {
        this.value = 1
      }
    })
  }

  // Add to wishlist functionality
  const wishlistBtn = document.querySelector(".wishlist")

  if (wishlistBtn) {
    wishlistBtn.addEventListener("click", function () {
      const heartIcon = this.querySelector("i")

      if (heartIcon.classList.contains("far")) {
        heartIcon.classList.remove("far")
        heartIcon.classList.add("fas")
        alert("Product added to wishlist!")
      } else {
        heartIcon.classList.remove("fas")
        heartIcon.classList.add("far")
        alert("Product removed from wishlist!")
      }
    })
  }
})