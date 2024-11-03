console.log("pixel.js загружен");
document.addEventListener("DOMContentLoaded", function() {
    const domain = window.location.hostname;

    console.log("Checking for visited cookie...");

    // Проверка существования куки
    if (!document.cookie.split('; ').find(row => row.startsWith('visited='))) {
        console.log("Cookie 'visited' не найдена. Устанавливаем куку...");

        // Проверяем домен в базе данных
        console.log(`Checking domain: ${domain}`);
        fetch(`/domain/check?domain=${domain}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.cookie = "visited=true; path=/; max-age=86400; SameSite=Lax";
                    console.log("Cookie 'visited' установлена:", document.cookie);

                    // Собираем данные о визите
                    const payload = {
                        page: window.location.href,
                        domain: domain,
                        visitTime: new Date().toISOString(),
                        ip: '',
                        userAgent: navigator.userAgent,
                        browser: navigator.appName,
                        device: navigator.platform,
                        platform: navigator.oscpu || navigator.platform
                    };

                    console.log("Payload for visit log:", payload);

                    fetch('/visit/log', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(data => console.log("Response from visit log:", data))
                    .catch(err => console.error("Error sending visit log:", err));
                }
            });
    } else {
        console.log("Cookie 'visited' уже установлена:", document.cookie);
    }
});
