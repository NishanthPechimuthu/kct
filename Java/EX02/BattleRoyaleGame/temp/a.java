class f {

    public String aa = "Hello";
}

class b {
    String cc = "Hello";
}

public class a extends f,b
{

    public static void main(String[] args) {
        a obj = new a();
        System.out.println(obj.aa);
    }
}