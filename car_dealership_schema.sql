-- ============================
-- CREATE DATABASE
-- ============================
CREATE DATABASE IF NOT EXISTS car_dealership;
USE car_dealership;

-- ============================
-- LOOKUP TABLES
-- ============================

CREATE TABLE Feature (
	feature_id INT AUTO_INCREMENT PRIMARY KEY,
	feature_name VARCHAR(100) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Vehicle_Condition_Type (
	condition_id INT AUTO_INCREMENT PRIMARY KEY,
	condition_name VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Body_Style (
	body_style_id INT AUTO_INCREMENT PRIMARY KEY,
	style VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Drivetrain (
	drivetrain_id INT AUTO_INCREMENT PRIMARY KEY,
	drivetrain VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Transmission (
	transmission_id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Fuel_Type (
	fuel_type_id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Role (
	role_id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(50) NOT NULL,
	permission VARCHAR(100),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- ADDRESS & USER
-- ============================

CREATE TABLE Address (
	address_id INT AUTO_INCREMENT PRIMARY KEY,
	street_name VARCHAR(100),
	street_number VARCHAR(10),
	apt_number VARCHAR(10),
	city VARCHAR(50),
	state VARCHAR(50),
	zip VARCHAR(10),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE User (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	address_id INT,
	fname VARCHAR(50),
	lname VARCHAR(50),
	phone VARCHAR(20),
	email VARCHAR(100) UNIQUE,
	password VARCHAR(255) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

-- ============================
-- STORE & STAFF
-- ============================

CREATE TABLE Store (
	store_id INT AUTO_INCREMENT PRIMARY KEY,
	manager_id INT,
	address_id INT,
	name VARCHAR(100),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (manager_id) REFERENCES User(user_id),
	FOREIGN KEY (address_id) REFERENCES Address(address_id)
);

CREATE TABLE Staff (
	user_id INT,
	store_id INT,
	role_id INT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (user_id, store_id),
	FOREIGN KEY (user_id) REFERENCES User(user_id),
	FOREIGN KEY (store_id) REFERENCES Store(store_id),
	FOREIGN KEY (role_id) REFERENCES Role(role_id)
);

-- ============================
-- VEHICLE
-- ============================

CREATE TABLE Vehicle (
	vin VARCHAR(17) PRIMARY KEY,
	body_style_id INT,
	drivetrain_id INT,
	transmission_id INT,
	fuel_type_id INT,
	store_id INT,
	brand VARCHAR(50),
	model VARCHAR(50),
	year INT,
	mileage INT,
	price DECIMAL(10,2),
	interior_color VARCHAR(30),
	exterior_color VARCHAR(30),
	seating_capacity INT,
	mpg_city INT,
	mpg_highway INT,
	ev_range INT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (body_style_id) REFERENCES Body_Style(body_style_id),
	FOREIGN KEY (drivetrain_id) REFERENCES Drivetrain(drivetrain_id),
	FOREIGN KEY (transmission_id) REFERENCES Transmission(transmission_id),
	FOREIGN KEY (fuel_type_id) REFERENCES Fuel_Type(fuel_type_id),
	FOREIGN KEY (store_id) REFERENCES Store(store_id)
);

-- ============================
-- MANY-TO-MANY JUNCTION TABLES
-- ============================

CREATE TABLE Vehicle_Feature (
	vin VARCHAR(17),
	feature_id INT,
	PRIMARY KEY (vin, feature_id),
	FOREIGN KEY (vin) REFERENCES Vehicle(vin),
	FOREIGN KEY (feature_id) REFERENCES Feature(feature_id)
);

CREATE TABLE Vehicle_Condition (
	vin VARCHAR(17),
	condition_id INT,
	PRIMARY KEY (vin, condition_id),
	FOREIGN KEY (vin) REFERENCES Vehicle(vin),
	FOREIGN KEY (condition_id) REFERENCES Vehicle_Condition_Type(condition_id)
);

-- ============================
-- PURCHASE
-- ============================

CREATE TABLE Purchase (
	purchase_id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT,
	vin VARCHAR(17),
	price DECIMAL(10,2),
	purchase_date DATE,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES User(user_id),
	FOREIGN KEY (vin) REFERENCES Vehicle(vin)
);

