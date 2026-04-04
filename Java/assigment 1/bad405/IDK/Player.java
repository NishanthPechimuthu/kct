package bad405.IDK;

// Encapsulation + Access Modifiers
public class Player {

    private int playerId;
    private String playerName;
    protected String gameType;

    // Constructor
    public Player(int playerId, String playerName, String gameType) {
        this.playerId = playerId;
        this.playerName = playerName;
        this.gameType = gameType;
    }

    // Reusable Method
    public void printBasicDetails() {
        System.out.println("Player ID: " + playerId);
        System.out.println("Player Name: " + playerName);
        System.out.println("Game Type: " + gameType);
    }

    // Getter & Setter (Encapsulation)
    public int getPlayerId() {
        return playerId;
    }

    public void setPlayerId(int playerId) {
        this.playerId = playerId;
    }

    public String getPlayerName() {
        return playerName;
    }

    // Method to be overridden
    public void displayDetails() {
        printBasicDetails();
    }
}