create table products (
    id uuid primary key not null,
    name varchar(255) not null,
    price integer not null,
    status varchar(100) not null
);