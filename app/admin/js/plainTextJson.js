function plainTextJson($jsonString)
{
    $decoded = json_decode($jsonString, true); // Use associative array mode

    // Check if JSON decoding was successful
    if (json_last_error() === JSON_ERROR_NONE) {
        if (is_array($decoded)) {
            // Convert array to a plain string (key-value pairs for associative arrays)
            return implode(", ", array_map(function ($key, $value) {
                if (is_array($value)) {
                    // Handle nested arrays
                    return "$key: [" . implode(", ", $value) . "]";
                }
                return "$key: $value";
            }, array_keys($decoded), $decoded));
        } elseif (is_string($decoded)) {
            return $decoded;
        }
    }

    return htmlspecialchars($jsonString);
}