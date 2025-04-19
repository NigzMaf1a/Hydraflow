import Trolley from "./trolley";
import ProductManager from "./productManager";

export class Cart extends Trolley{
    constructor(){
        super();
        this.productManager = new ProductManager();
    }
    addItem(item){
        super.addItem();
    }
    removeItem(item){
        return super.removeItem();
    }
    getItems(){
        return super.getItems();
    }
}