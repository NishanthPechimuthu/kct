public class Main {
    public static void main(String[] args) {

        Soldier s = new Soldier("Black");
        s.displayRole();
        s.attack();
        s.heal();
        s.showHealth();

        System.out.println("----------------");

        Medic m = new Medic("White");
        m.displayRole();
        m.heal();
        m.revive();

        System.out.println("----------------");

        Tank t = new Tank("Gray");
        t.displayRole();
        t.attack();
        t.heal();
        t.revive();

        System.out.println("----------------");

        AmphibiousVehicle av = new AmphibiousVehicle();
        av.move();
    }
}