abstract class Character {
    String name;
    int health;

    Character(String name, int health) {
        this.name = name;
        this.health = health;
    }

    abstract void displayRole();

    void showHealth() {
        System.out.println(name + " Health: " + health);
    }
}

class Soldier extends Character implements Attackable, Healable {

    Soldier(String name) {
        super(name, 100);
    }

    @Override
    public void attack() {
        System.out.println(name + " shoots enemy!");
    }

    @Override
    public void heal() {
        health += 20;
        System.out.println(name + " uses medkit!");
    }

    @Override
    void displayRole() {
        System.out.println("Role: Soldier");
    }
}

class Medic extends Character implements Healable, Revivable {

    Medic(String name) {
        super(name, 80);
    }

    @Override
    public void heal() {
        System.out.println(name + " heals teammate!");
    }

    @Override
    public void revive() {
        System.out.println(name + " revives a teammate!");
    }

    @Override
    void displayRole() {
        System.out.println("Role: Medic");
    }
}

class Sniper extends Character implements Attackable {

    Sniper(String name) {
        super(name, 70);
    }

    @Override
    public void attack() {
        System.out.println(name + " snipes from distance!");
    }

    @Override
    void displayRole() {
        System.out.println("Role: Sniper");
    }
}

class Tank extends Character implements Attackable, Healable, Revivable {

    Tank(String name) {
        super(name, 200);
    }

    @Override
    public void attack() {
        System.out.println(name + " fires heavy cannon!");
    }

    @Override
    public void heal() {
        System.out.println(name + " auto-repairs armor!");
    }

    @Override
    public void revive() {
        System.out.println(name + " rescues fallen teammate!");
    }

    @Override
    void displayRole() {
        System.out.println("Role: Tank");
    }
}

class AmphibiousVehicle implements LandMovement, WaterMovement {

    @Override
    public void move() {
        LandMovement.super.move();
        WaterMovement.super.move();
        System.out.println("Amphibious vehicle can move on both terrains!");
    }
}