import bad405.IDK.*;

public class MainApp {

    public static void main(String[] args) {

        int[] scores = { 150, 220, 300, 275, 190 };

        ProPlayer pro = new ProPlayer(1, "Mr. BLK", "FPS", scores);

        CasualPlayer casual = new CasualPlayer(2, "Mr. White", "RPG", "Adventure");

        System.out.println("=== Pro Player ===");
        pro.displayDetails();

        System.out.println("\n=== Casual Player ===");
        casual.displayDetails();

        System.out.println("\n=== Method Overloading Demo ===");
        casual.showMode();
        casual.showMode("Gold Tier");
    }
}