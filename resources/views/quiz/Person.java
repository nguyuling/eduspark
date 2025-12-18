// encapsulation
public class Person {
    private String name;
    // Getter
    public String getName() {
        return name;
    }

    // Setter
    public void setName(String newName) {
        this.name = newName;
    }
}

// absctraction
abstract class Animal {
    public abstract void animalSound();
    public void sleep() {
        System.out.println("Zzz");
    }
}

// Create a subclass called Rabbit (inherit from Animal)
class Rabbit extends Animal {
    public void animalSound() {
        System.out.println("The rabbit says: carrot carrot");
    }
}

class Main {
    public static void main(String[] args) {
        // Create a Rabbit object
        Rabbit myRabbit = new Rabbit();
        // Call the animalSound() method
        myRabbit.animalSound();
        // Call the sleep() method
        myRabbit.sleep();
    }
}

// inheritance
class Vehicle {
    protected String brand = "Ford";
    public void honk() {
        System.out.println("Tuut, tuut!");
    }
}

class Car extends Vehicle {
    private String modelName = "Mustang";
    public static void main(String[] args) {
        // Create a myCar object
        Car myCar = new Car();
        // Call the honk() method
        myCar.honk();
        // Print the brand of the car
        System.out.println(myCar.brand);
        // Print the model name of the car
        System.out.println(myCar.modelName);
    }
}

// polymorphism
class Animal {
    public void animalSound() {
        System.out.println("The animal makes a sound");
    }
}

class Rabbit extends Animal {
    public void animalSound() {
        System.out.println("The rabbit says: carrot carrot");
    }
}

class Cat extends Animal {
    public void animalSound() {
        System.out.println("The cat says: miaw miaw");
    }
}

class Main {
    public static void main(String[] args) {
        // Create a Animal object
        Animal myAnimal = new Animal();
        // Create a Rabbit object
        Animal myRabbit = new Rabbit();
        // Create a Cat object
        Animal myCat = new Cat();
        // Call the animalSound() method on each object
        myAnimal.animalSound();
        myRabbit.animalSound();
        myCat.animalSound();
    }
}
