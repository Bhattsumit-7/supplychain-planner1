so now the table is same code is :

supply_chain ;
CREATE TABLE orders (
    PRODUCT_ID VARCHAR(50) PRIMARY KEY,
    PRODUCT_NAME VARCHAR(100),
    QUANTITY INT,
    ORDER_DATE DATE
);
CREATE TABLE inventory (
    PRODUCT_ID VARCHAR(50) PRIMARY KEY,
    LEAD_TIME INT
);
