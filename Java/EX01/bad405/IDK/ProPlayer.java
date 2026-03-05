package bad405.IDK;

// Inheritance
public class ProPlayer extends Player {

    private int[] scores; // Array

    public ProPlayer(int playerId, String playerName, String gameType, int[] scores) {
        super(playerId, playerName, gameType);
        this.scores = scores;
    }

    // Control Statement + Array Processing
    public double calculateAverageScore() {
        int sum = 0;

        // for-each loop
        for (int s : scores) {
            sum += s;
        }

        return (double) sum / scores.length;
    }

    // Method Overriding
    @Override
    public void displayDetails() {
        super.displayDetails();

        System.out.println("Scores:");
        for (int s : scores) {
            System.out.println(s);
        }

        System.out.println("Average Score: " + calculateAverageScore());
    }
}