package bad405.IDK;

// Inheritance
public class CasualPlayer extends Player {

    private String favoriteMode;

    public CasualPlayer(int playerId, String playerName, String gameType, String favoriteMode) {
        super(playerId, playerName, gameType);
        this.favoriteMode = favoriteMode;
    }

    // Method Overriding
    @Override
    public void displayDetails() {
        super.displayDetails();
        System.out.println("Favorite Mode: " + favoriteMode);
    }

    // Method Overloading
    public void showMode() {
        System.out.println("Mode: " + favoriteMode);
    }

    public void showMode(String rank) {
        System.out.println("Mode: " + favoriteMode + " | Rank: " + rank);
    }
}