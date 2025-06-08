<?php

class SlidesController extends Controller
{
    public function updateOrder()
    {
        // Изключваме извеждането на грешки
        error_reporting(0);
        ini_set('display_errors', 0);
        
        // Изчистваме буфера за да сме сигурни, че няма изведен текст преди JSON
        if (ob_get_length()) ob_clean();
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $rawInput = file_get_contents('php://input');
            error_log("Received raw input: " . $rawInput);
            
            if (empty($rawInput)) {
                throw new Exception('No input data received');
            }

            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data: ' . json_last_error_msg());
            }
            
            if (!isset($data['slides']) || !is_array($data['slides'])) {
                throw new Exception('Invalid slides data');
            }

            error_log("Processing slides order: " . print_r($data['slides'], true));

            $slideModel = new Slide();
            $success = true;
            $errorMessage = '';

            foreach ($data['slides'] as $index => $slideId) {
                error_log("Updating slide ID: $slideId to order: " . ($index + 1));
                if (!$slideModel->updateOrder($slideId, $index + 1)) {
                    $success = false;
                    $errorMessage = "Failed to update slide ID: $slideId";
                    error_log("Failed to update slide order. Slide ID: $slideId, New order: " . ($index + 1));
                    break;
                }
            }

            header('Content-Type: application/json');
            $response = [
                'success' => $success,
                'message' => $success ? 'Slide order updated successfully' : $errorMessage
            ];
            error_log("Sending response: " . json_encode($response));
            echo json_encode($response);
        } catch (Exception $e) {
            error_log("Error in updateOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        } catch (Error $e) {
            error_log("PHP Error in updateOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
        
        // Спираме изпълнението след изпращане на JSON
        exit;
    }
} 