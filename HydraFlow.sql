CREATE DATABASE IF NOT EXISTS hydraflow;
USE hydraflow;

-- Create Table Registration
CREATE TABLE Registration (
    RegID INT AUTO_INCREMENT PRIMARY KEY,
    Name1 VARCHAR(255) NOT NULL,
    Name2 VARCHAR(255) NOT NULL,
    PhoneNo VARCHAR(15) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Gender ENUM('Male', 'Female') NOT NULL,
    RegType ENUM('Admin', 'Manager', 'Client', 'Plumber', 'Mason') NOT NULL,
    dLocation VARCHAR(255),
    accStatus ENUM('Pending', 'Approved', 'Inactive') DEFAULT 'Pending',
    lastAccessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
); 
CREATE TABLE Admin (
    AdminID INT PRIMARY KEY,
    FOREIGN KEY (AdminID) REFERENCES Registration(RegID),
);

-- Create Table Manager
CREATE TABLE Manager (
    ManagerID INT PRIMARY KEY,
    FOREIGN KEY (ManagerID) REFERENCES Registration(RegID),
    Balance INT DEFAULT 0
);

-- Create Table Plumber
CREATE TABLE Plumber (
    PlumberID INT PRIMARY KEY,
    FOREIGN KEY (PlumberID) REFERENCES Registration(RegID),
    Balance INT DEFAULT 0
);

-- Create Table Client
CREATE TABLE Client (
    ClientID INT PRIMARY KEY,
    FOREIGN KEY (ClientID) REFERENCES Registration(RegID),
    Balance INT DEFAULT 0
);

-- Create Table Mason
CREATE TABLE Mason (
    MasonID INT PRIMARY KEY,
    FOREIGN KEY (MasonID) REFERENCES Registration(RegID),
    Balance INT DEFAULT 0
);

-- Create Table Property
CREATE TABLE Property (
    PropertyID INT AUTO_INCREMENT PRIMARY KEY,
    ClientID INT NOT NULL,
    PropertyName VARCHAR(255) UNIQUE NOT NULL,
    PropertyType ENUM('Residential', 'Apartments') NOT NULL,
    NumberUnits INT,
    FOREIGN KEY (ClientID) REFERENCES Client(ClientID)
);

-- Create Table Unit
CREATE TABLE Unit (
    UnitID INT AUTO_INCREMENT PRIMARY KEY,
    PropertyID INT NOT NULL,
    UnitName VARCHAR(255) NOT NULL,
    FOREIGN KEY (PropertyID) REFERENCES Property(PropertyID)
);

-- Create Table Job
CREATE TABLE Job (
    JobID INT AUTO_INCREMENT PRIMARY KEY,
    PropertyID INT NOT NULL,
    UnitID INT NOT NULL,
    JobLocation VARCHAR(255) NOT NULL,
    JobType ENUM('Repair', 'Installation') NOT NULL,
    JobDescription VARCHAR(255) NOT NULL,
    Charges INT NOT NULL,
    Paid ENUM('YES', 'NO') DEFAULT 'NO',
    Approved ENUM('YES', 'NO') DEFAULT 'NO',
    JobStatus ENUM('Pending', 'Completed') DEFAULT 'Pending',
    FOREIGN KEY (PropertyID) REFERENCES Property(PropertyID),
    FOREIGN KEY (UnitID) REFERENCES Unit(UnitID)
);

-- Create Table Booking
CREATE TABLE Booking (
    BookingID INT AUTO_INCREMENT PRIMARY KEY,
    JobID INT NOT NULL,
    BookDate DATE NOT NULL,
    Charges INT NOT NULL,
    BookType ENUM('Installation', 'Repair') NOT NULL,
    BookApprove ENUM('YES', 'NO') DEFAULT 'NO',
    Completed ENUM('YES', 'NO') DEFAULT 'NO',
    FOREIGN KEY (JobID) REFERENCES Job(JobID)
);

--Create Table Tools
CREATE TABLE Tools (
    ToolID INT AUTO_INCREMENT PRIMARY KEY,
    ToolName VARCHAR(255) NOT NULL,
    ToolDescription VARCHAR(255) NOT NULL,
    ToolCondition ENUM('Cat1', 'Cat2', 'Cat3', 'Cat4'),
    Price INT NOT NULL,
    ToolUnits INT DEFAULT 0,
    Available ENUM('Yes', 'No') NOT NULL
);

-- Create Table Product
CREATE TABLE Product (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    ProductName VARCHAR(255) NOT NULL,
    ProductDescription VARCHAR(255) NOT NULL,
    Price INT NOT NULL,
    ProductUnits INT DEFAULT 0,
    Available ENUM('Yes', 'No') NOT NULL
);

-- Create Table Order
CREATE TABLE ClientOrder (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    ClientID INT NOT NULL,
    ProductID INT NOT NULL,
    OrderDate DATE NOT NULL,
    Quantity INT NOT NULL,
    Price INT NOT NULL,
    Paid ENUM('YES', 'NO') DEFAULT 'NO',
    FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- Create Table Quotation
CREATE TABLE Quotation (
    QuotationID INT AUTO_INCREMENT PRIMARY KEY,
    ClientID INT NOT NULL,
    JobID INT NOT NULL,
    QuotationDate DATE NOT NULL,
    QuotationAmount INT NOT NULL,
    Status ENUM('Pending', 'Approved', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
    FOREIGN KEY (JobID) REFERENCES Job(JobID)
);

-- Create Table Quotation Items
CREATE TABLE QuotationItems (
    ItemID INT AUTO_INCREMENT PRIMARY KEY,
    QuotationID INT NOT NULL,
    ItemName VARCHAR(255) NOT NULL,
    Quantity INT NOT NULL,
    Price INT NOT NULL,
    FOREIGN KEY (QuotationID) REFERENCES Quotation(QuotationID)
);

-- Create Table Payment
CREATE TABLE Payment (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    ClientID INT NOT NULL,
    JobID INT NOT NULL,
    PaymentDate DATE NOT NULL,
    PaymentAmount INT NOT NULL,
    PaymentType ENUM('Cash', 'Mpesa') NOT NULL,
    ApprovePay ENUM('YES', 'NO') DEFAULT 'NO',
    PaymentStatus ENUM('Pending', 'Completed') DEFAULT 'Pending',
    FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
    FOREIGN KEY (JobID) REFERENCES Job(JobID)
);

-- Create Table Funds
CREATE TABLE Funds (
    FundID INT AUTO_INCREMENT PRIMARY KEY,
    PaymentID INT NOT NULL,
    Amount INT NOT NULL,
    PaymentDate DATE NOT NULL,
    Total INT,
    FOREIGN KEY (PaymentID) REFERENCES Payment(PaymentID)
);

-- Create Table PayWorker
CREATE TABLE PayWorker (
    PayWorkerID INT AUTO_INCREMENT PRIMARY KEY,
    RegID INT NOT NULL,
    RegType ENUM('Admin', 'Manager', 'Plumber', 'Mason') NOT NULL,
    Rate INT NOT NULL,
    Amount INT,
    PayDate DATE NOT NULL,
    FOREIGN KEY (RegID) REFERENCES Registration(RegID)
);

-- Create Table AssignWork
CREATE TABLE AssignWork (
    AssignID INT AUTO_INCREMENT PRIMARY KEY,
    ManagerID INT NOT NULL,
    JobID INT NOT NULL,
    AssignDate DATE NOT NULL,
    StartDate DATE,
    EndDate DATE,
    FOREIGN KEY (ManagerID) REFERENCES Manager(ManagerID),
    FOREIGN KEY (JobID) REFERENCES Job(JobID)
);

-- Create Table PlumberAssignWork
CREATE TABLE PlumberAssignWork (
    PlumberAssignID INT AUTO_INCREMENT PRIMARY KEY,
    AssignID INT NOT NULL,
    PlumberID INT NOT NULL,
    FOREIGN KEY (AssignID) REFERENCES AssignWork(AssignID),
    FOREIGN KEY (PlumberID) REFERENCES Plumber(PlumberID)
);

--Create Table Reviews 
CREATE TABLE Reviews (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    ClientID INT NOT NULL,
    ProductID INT,
    PlumberID INT,
    Rating INT NOT NULL CHECK (Rating BETWEEN 1 AND 5),
    ReviewText TEXT,
    ReviewDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ClientID) REFERENCES Client(ClientID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
    FOREIGN KEY (PlumberID) REFERENCES Plumber(PlumberID)
);

--Create Table FAQs 
CREATE TABLE FAQs (
    FAQID INT AUTO_INCREMENT PRIMARY KEY,
    Question VARCHAR(255) NOT NULL,
    Answer TEXT NOT NULL,
    CreatedDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
