<?php


$phone = 'bopA';

        // Ensure it starts with '92' (Pakistan code)
        if (preg_match('/op(?=A)/', $phone, $matches)) {
            print_r($matches);
            
        }