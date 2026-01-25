-- FiveM Script untuk Absensi Otomatis
-- Script ini mengirim data absensi ke Laravel API

local API_URL = "https://medicalcenterime.my.id/api/absensi" -- Ganti dengan URL website Laravel Anda
local API_KEY = "45b712ffc6bae5375bdc2be08d487a91422d430d75cfea3390a37ae52817aa11" -- WAJIB: untuk autentikasi API

-- WARNING: API key ini seharusnya disimpan di server config, bukan di script client
-- Untuk keamanan maksimal, gunakan server-side authentication atau environment variables

-- Fungsi untuk mengirim data absensi ke Laravel API
function SendAbsensiData(playerId, playerName, clockIn, clockOut, timeOnDuty)
    -- Validasi input
    if not playerId or not playerName or not clockIn then
        print('^1[ABSENSI ERROR]^7 Invalid data provided to SendAbsensiData')
        return false
    end
    
    local data = {
        player_id = tostring(playerId),
        player_name = tostring(playerName),
        clock_in = tostring(clockIn),
        clock_out = clockOut and tostring(clockOut) or nil,
        time_on_duty = timeOnDuty and tostring(timeOnDuty) or nil
    }
    
    -- Headers untuk request
    local headers = {
        ['Content-Type'] = 'application/json',
        ['X-API-Key'] = API_KEY
    }
    
    PerformHttpRequest(API_URL, function(err, text, headers)
        if err ~= 200 then
            print('^1[ABSENSI ERROR]^7 Error sending absensi data: ' .. err)
            TriggerClientEvent('chat:addMessage', -1, {
                color = {255, 0, 0},
                multiline = true,
                args = {"[ABSENSI]", "Error mengirim data absensi: " .. err}
            })
        else
            local success, response = pcall(json.decode, text)
            if not success then
                print('^1[ABSENSI ERROR]^7 Failed to parse JSON response: ' .. tostring(response))
                TriggerClientEvent('chat:addMessage', -1, {
                    color = {255, 0, 0},
                    multiline = true,
                    args = {"[ABSENSI]", "Error parsing response dari server"}
                })
                return
            end
            
            if response and response.success then
                print('^2[ABSENSI SUCCESS]^7 ' .. response.message)
                TriggerClientEvent('chat:addMessage', -1, {
                    color = {0, 255, 0},
                    multiline = true,
                    args = {"[ABSENSI]", response.message}
                })
            else
                local errorMsg = response and response.message or 'Unknown error'
                local errorCode = response and response.error_code or 'UNKNOWN'
                print('^3[ABSENSI WARNING]^7 API Error: ' .. errorMsg .. ' (Code: ' .. errorCode .. ')')
                TriggerClientEvent('chat:addMessage', -1, {
                    color = {255, 165, 0},
                    multiline = true,
                    args = {"[ABSENSI]", "API Error: " .. errorMsg}
                })
            end
        end
    end, 'POST', json.encode(data), headers)
    
    return true
end

-- Fungsi untuk mendapatkan waktu saat ini dalam format yang benar
function GetCurrentTime()
    return os.date("%Y-%m-%d %H:%M:%S")
end

-- Fungsi untuk menghitung durasi kerja
function CalculateWorkDuration(clockIn, clockOut)
    local inTime = os.time({
        year = tonumber(string.sub(clockIn, 1, 4)),
        month = tonumber(string.sub(clockIn, 6, 7)),
        day = tonumber(string.sub(clockIn, 9, 10)),
        hour = tonumber(string.sub(clockIn, 12, 13)),
        min = tonumber(string.sub(clockIn, 15, 16)),
        sec = tonumber(string.sub(clockIn, 18, 19))
    })
    
    local outTime = os.time({
        year = tonumber(string.sub(clockOut, 1, 4)),
        month = tonumber(string.sub(clockOut, 6, 7)),
        day = tonumber(string.sub(clockOut, 9, 10)),
        hour = tonumber(string.sub(clockOut, 12, 13)),
        min = tonumber(string.sub(clockOut, 15, 16)),
        sec = tonumber(string.sub(clockOut, 18, 19))
    })
    
    local duration = outTime - inTime
    local hours = math.floor(duration / 3600)
    local minutes = math.floor((duration % 3600) / 60)
    local seconds = duration % 60
    
    return string.format("%02d:%02d:%02d", hours, minutes, seconds)
end

-- Event untuk clock in
RegisterServerEvent('absensi:clockIn')
AddEventHandler('absensi:clockIn', function()
    local source = source
    local playerId = GetPlayerIdentifier(source, 0)
    local playerName = GetPlayerName(source)
    local clockInTime = GetCurrentTime()
    
    -- Kirim data clock in
    SendAbsensiData(playerId, playerName, clockInTime, nil, nil)
    
    -- Simpan data clock in di server (opsional)
    TriggerClientEvent('absensi:clockInSuccess', source, clockInTime)
end)

-- Event untuk clock out
RegisterServerEvent('absensi:clockOut')
AddEventHandler('absensi:clockOut', function(clockInTime)
    local source = source
    local playerId = GetPlayerIdentifier(source, 0)
    local playerName = GetPlayerName(source)
    local clockOutTime = GetCurrentTime()
    local timeOnDuty = CalculateWorkDuration(clockInTime, clockOutTime)
    
    -- Kirim data clock out
    SendAbsensiData(playerId, playerName, clockInTime, clockOutTime, timeOnDuty)
    
    -- Simpan data clock out di server (opsional)
    TriggerClientEvent('absensi:clockOutSuccess', source, clockOutTime, timeOnDuty)
end)

-- Command untuk test API
RegisterCommand('testabsensi', function(source, args, rawCommand)
    local playerId = GetPlayerIdentifier(source, 0)
    local playerName = GetPlayerName(source)
    local currentTime = GetCurrentTime()
    
    -- Test clock in
    SendAbsensiData(playerId, playerName, currentTime, nil, nil)
    
    -- Test clock out setelah 5 detik
    SetTimeout(5000, function()
        local clockOutTime = GetCurrentTime()
        local timeOnDuty = CalculateWorkDuration(currentTime, clockOutTime)
        SendAbsensiData(playerId, playerName, currentTime, clockOutTime, timeOnDuty)
    end)
end, false)

-- Command untuk cek status absensi
RegisterCommand('cekabsensi', function(source, args, rawCommand)
    local playerId = GetPlayerIdentifier(source, 0)
    
    -- Cek status via API
    PerformHttpRequest(API_URL .. '/status/' .. playerId, function(err, text, headers)
        if err == 200 then
            local response = json.decode(text)
            if response and response.success then
                local status = response.data.is_active and "Aktif" or "Tidak Aktif"
                TriggerClientEvent('chat:addMessage', source, {
                    color = {0, 255, 0},
                    multiline = true,
                    args = {"[ABSENSI STATUS]", "Status: " .. status}
                })
            end
        end
    end, 'GET')
end, false)

print("^2[ABSENSI]^7 Script absensi otomatis berhasil dimuat!")
print("^2[ABSENSI]^7 Gunakan /testabsensi untuk test API")
print("^2[ABSENSI]^7 Gunakan /cekabsensi untuk cek status")
