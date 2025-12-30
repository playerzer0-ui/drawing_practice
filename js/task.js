async function downloadImage(url, id) {
    try {
        const response = await fetch(url, { mode: 'cors' });
        const blob = await response.blob();

        // Create temporary link
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = id + ".jpg";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Release memory
        URL.revokeObjectURL(link.href);
    } catch (err) {
        console.error("Download failed:", err);
        window.open(url, "_blank");
    }
}

