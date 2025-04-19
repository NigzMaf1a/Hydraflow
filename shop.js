import Product from "./product";

export default class Shop {
    constructor(userID) {
        this.userID = userID;
        this.product = new Product();
        this.cartItems = this.loadCart(); // Load from localStorage if available
    }

    // 🛒 Display products on the shelf
    async shopShelf(productsContainer) {
        try {
            productsContainer.innerHTML = "";
            const allItems = await this.product.getProducts();
            const shelfItems = allItems.filter(item => item.Available === 'YES');

            shelfItems.forEach(item => {
                const itemDiv = document.createElement("div");
                itemDiv.classList.add('cont1');

                const nameP = document.createElement("p");
                nameP.classList.add('p');
                nameP.textContent = `Name: ${item.ProductName}`;

                const descP = document.createElement("p");
                descP.classList.add('p');
                descP.textContent = `Description: ${item.ProductDescription}`;

                const priceP = document.createElement("p");
                priceP.classList.add('p');
                priceP.textContent = `Price: $${item.Price}`;

                const imageDiv = document.createElement("div");
                imageDiv.classList.add('imDiv');
                imageDiv.style.backgroundImage = `url(${item.ProductImage})`;

                const addBtn = document.createElement("button");
                addBtn.classList.add("add-btn");
                addBtn.textContent = "Add to Cart";
                addBtn.addEventListener("click", () => {
                    this.addToCart(item);
                });

                itemDiv.appendChild(nameP);
                itemDiv.appendChild(descP);
                itemDiv.appendChild(priceP);
                itemDiv.appendChild(imageDiv);
                itemDiv.appendChild(addBtn);

                productsContainer.appendChild(itemDiv);
            });
        } catch (error) {
            console.error('Error getting product shelf:', error.message);
        }
    }

    // 🧠 Add product to cart
    addToCart(product) {
        const index = this.cartItems.findIndex(item => item.ProductID === product.ProductID);

        if (index !== -1) {
            this.cartItems[index].quantity += 1;
        } else {
            this.cartItems.push({ ...product, quantity: 1 });
        }

        this.saveCart();
        console.log(`${product.ProductName} added to cart`);
    }

    // 🔍 View current cart
    getCart() {
        return this.cartItems;
    }

    // 🔄 Update quantity of specific item
    updateQuantity(productID, newQty) {
        const item = this.cartItems.find(item => item.ProductID === productID);

        if (item) {
            if (newQty <= 0) {
                this.removeFromCart(productID);
            } else {
                item.quantity = newQty;
                this.saveCart();
                console.log(`Updated quantity to ${newQty}`);
            }
        }
    }

    // ❌ Remove item from cart
    removeFromCart(productID) {
        this.cartItems = this.cartItems.filter(item => item.ProductID !== productID);
        this.saveCart();
        console.log(`Removed item ${productID} from cart`);
    }

    // 🧹 Clear whole cart
    clearCart() {
        this.cartItems = [];
        this.saveCart();
        console.log("Cart cleared");
    }

    // 💾 Save to localStorage
    saveCart() {
        localStorage.setItem(`cart_${this.userID}`, JSON.stringify(this.cartItems));
    }

    // 🧲 Load from localStorage
    loadCart() {
        const data = localStorage.getItem(`cart_${this.userID}`);
        return data ? JSON.parse(data) : [];
    }
}
