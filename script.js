document.addEventListener("DOMContentLoaded", () => {
  const scheduleData = document.getElementById("schedule-data");
  const totalOrdersElem = document.getElementById("total-orders");
  const totalProductionDaysElem = document.getElementById("total-production-days");
  const averageLeadTimeElem = document.getElementById("average-lead-time");
  const clearButton = document.getElementById("clear-response");
  const uploadForm = document.getElementById("upload-form");
  const uploadMessage = document.getElementById("upload-message");

  // Fetch data and populate the table
  fetch("fetch_data.php")
    .then((response) => response.json())
    .then((data) => {
      let totalOrders = 0;
      let totalLeadTime = 0;
      const uniqueDates = new Set();

      data.forEach((row) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.ORDER_ID}</td>
          <td>${row.PRODUCT_ID}</td>
          <td>${row.PRODUCT_NAME}</td>
          <td>${row.QUANTITY}</td>
          <td>${row.LEAD_TIME || "N/A"}</td>
          <td>${row.ORDER_DATE}</td>
          <td>${row.ORDER_DATE}</td>
          <td>${Math.min(row.QUANTITY, 120)}</td>
          <td>${Math.min(row.QUANTITY - 120, 120) || 0}</td>
          <td>${Math.min(row.QUANTITY - 240, 120) || 0}</td>
          <td>${Math.min(row.QUANTITY - 360, 120) || 0}</td>
          <td>${row.QUANTITY * 15} minutes</td>
        `;
        scheduleData.appendChild(tr);

        totalOrders++;
        if (row.LEAD_TIME) totalLeadTime += parseInt(row.LEAD_TIME);
        uniqueDates.add(row.ORDER_DATE);
      });

      // Update summary
      totalOrdersElem.textContent = totalOrders;
      totalProductionDaysElem.textContent = uniqueDates.size;
      averageLeadTimeElem.textContent = (totalLeadTime / totalOrders || 0).toFixed(2);
    })
    .catch((error) => console.error("Error fetching data:", error));

  // Clear response functionality
  clearButton.addEventListener("click", () => {
    scheduleData.innerHTML = ""; // Clear table data
    totalOrdersElem.textContent = ""; // Clear total orders
    totalProductionDaysElem.textContent = ""; // Clear production days
    averageLeadTimeElem.textContent = ""; // Clear average lead time
    alert("Output cleared successfully!");
  });

  // Validate file uploads
  uploadForm.addEventListener("submit", (event) => {
    event.preventDefault(); // Prevent form submission

    const ordersFile = document.getElementById("orders").files[0];
    const inventoryFile = document.getElementById("inventory").files[0];

    // Check if files are selected
    if (!ordersFile || !inventoryFile) {
      uploadMessage.textContent = "Please upload both files.";
      uploadMessage.style.color = "red";
      return;
    }

    // Normalize file names (trim spaces and convert to lowercase)
    const ordersFileName = ordersFile.name.trim().toLowerCase();
    const inventoryFileName = inventoryFile.name.trim().toLowerCase();

    // Check if file names are correct (case-insensitive)
    if (ordersFileName !== "orders.csv" || inventoryFileName !== "inventory.csv") {
      uploadMessage.textContent = "Please upload the correct files: orders.csv and inventory.csv.";
      uploadMessage.style.color = "red";
      return;
    }

    // If files are correct, proceed with upload
    uploadMessage.textContent = "Uploading and processing files...";
    uploadMessage.style.color = "green";

    const formData = new FormData(uploadForm);

    // Send files to server using Fetch API
    fetch("upload.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((data) => {
        uploadMessage.textContent = data; // Display server response
        uploadMessage.style.color = "green";
        setTimeout(() => {
          window.location.reload(); // Reload the page to reflect changes
        }, 2000);
      })
      .catch((error) => {
        uploadMessage.textContent = "Error uploading files. Please try again.";
        uploadMessage.style.color = "red";
        console.error("Error:", error);
      });
  });
});