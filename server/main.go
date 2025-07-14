package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"

	"github.com/joho/godotenv"
)

type FormData struct {
	Title   string `json:"title"`
	Name    string `json:"name"`
	Phone   string `json:"phone"`
	Comment string `json:"comment"`
}

func sendTelegramMessage(token, chatID, text string) error {
	url := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", token)

	body := map[string]string{
		"chat_id":    chatID,
		"text":       text,
		"parse_mode": "HTML",
	}

	jsonBody, _ := json.Marshal(body)

	resp, err := http.Post(url, "application/json", bytes.NewBuffer(jsonBody))

	if err != nil {
		return err
	}
	defer resp.Body.Close()

	return nil
}

func handler(w http.ResponseWriter, r *http.Request) {
	// Разрешаем CORS
	w.Header().Set("Access-Control-Allow-Origin", "*") // в проде — указывай домен
	w.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS")
	w.Header().Set("Access-Control-Allow-Headers", "Content-Type")

	// Обработка preflight запроса
	if r.Method == http.MethodOptions {
		w.WriteHeader(http.StatusOK)
		return
	}

	if r.Method != http.MethodPost {
		http.Error(w, "Only POST allowed", http.StatusMethodNotAllowed)
		return
	}

	var form FormData
	if err := json.NewDecoder(r.Body).Decode(&form); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	message := fmt.Sprintf("<b>%s</b>\nИмя: %s\nТелефон: %s\nКомментарий: %s",
		form.Title, form.Name, form.Phone, form.Comment)

	err := sendTelegramMessage(os.Getenv("TELEGRAM_BOT_TOKEN"), os.Getenv("TELEGRAM_CHAT_ID"), message)
	if err != nil {
		http.Error(w, "Ошибка отправки", http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
}

func main() {
	err := godotenv.Load("config/.env")
	if err != nil {
		log.Fatal("Ошибка загрузки .env файла")
	}

	http.HandleFunc("/send", handler)

	port := os.Getenv("PORT")
	fmt.Println("Сервер запущен на порту:", port)
	log.Fatal(http.ListenAndServe(":"+port, nil))
}
