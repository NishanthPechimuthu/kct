interface Attackable {
    void attack();
}

interface Healable {
    void heal();
}

interface Revivable {
    void revive();
}

interface LandMovement {
    default void move() {
        System.out.println("Moving on land...");
    }
}

interface WaterMovement {
    default void move() {
        System.out.println("Moving in water...");
    }
}