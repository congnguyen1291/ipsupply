<?php
class MySQLConnectionExample {
    public function static void main(String args[]) throws SQLException {
        Connection mysqlCon = null;
        try {
            Class.forName("com.mysql.jdbc.Driver");
            mysqlCon = DriverManager.getConnection("jdbc:mysql://localhost:3306/test", "congnguyen", "congnguyen");
        } catch (Exception e) {
            System.out.println(e);
        }

		}
}
?>