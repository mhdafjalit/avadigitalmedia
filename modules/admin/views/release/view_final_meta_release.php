<?php
$this->load->view('top_application',['has_header'=>false,'ws_page'=>'store_pg','is_popup'=>true,'has_body_style'=>'padding:0']);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
$prim_track_types 	= $this->config->item('prim_track_types');
$lang_arr 			= $this->config->item('lang_arr');
$artist_name = get_db_field_value('wl_artists','name',['pdl_id'=>$res['artist_name']]);
$total_territories = count_record ('wl_release_territories',"release_id='".$res['release_id']."'");
$total_release_stores = count_record ('wl_release_stores',"release_id='".$res['release_id']."'");

// Status mapping
$status_value = $res['status'] ?? 0;
$badge_class = '';
$status_text = '';
switch($status_value) {
    case '1': $badge_class = 'badge-success'; $status_text = 'Active'; break;
    case '2': $badge_class = 'badge-warning'; $status_text = 'Pending'; break;
    case '3': $badge_class = 'badge-danger'; $status_text = 'Inactive'; break;
    default: $badge_class = 'badge-info'; $status_text = 'Draft';
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef5f9 0%, #faf5fe 100%);
            padding: 0;
            margin: 0;
        }
        
        /* Custom Color Variables */
        :root {
            --gradient-start: #de0c78;
            --gradient-end: #8830a2;
            --primary: #de0c78;
            --primary-dark: #b80a64;
            --primary-light: #fce4f2;
            --secondary: #8830a2;
            --secondary-light: #f3e8f8;
        }
        
        .modal-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.4s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header Styles */
        .modal-header-custom {
            background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
            padding: 30px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .modal-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            pointer-events: none;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .close-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
        }
        
        .close-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }
        
        /* Hero Section */
        .hero-section {
            display: flex;
            gap: 30px;
            padding: 30px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .album-cover {
            flex-shrink: 0;
        }
        
        .album-cover img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .album-cover img:hover {
            transform: scale(1.02);
        }
        
        .album-info {
            flex: 1;
        }
        
        .album-title {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }
        
        .artist-name {
            font-size: 18px;
            color: #6b7280;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }
        
        .badge-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .badge-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .badge-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
        .badge-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 30px;
            background: #f9fafb;
        }
        
        .stat-item {
            background: white;
            padding: 16px;
            border-radius: 16px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-color: #de0c78;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #de0c78;
            margin-bottom: 8px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Info Sections */
        .info-section {
            padding: 30px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1f2937;
        }
        
        .section-title i {
            color: #de0c78;
            font-size: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-card {
            background: #f9fafb;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            border-color: #de0c78;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .info-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #de0c78;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
            word-break: break-word;
        }
        
        /* Track Section */
        .track-section {
            background: linear-gradient(135deg, #fef5f9 0%, #faf5fe 100%);
        }
        
        .track-item {
            background: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        
        .track-item:hover {
            border-color: #de0c78;
            box-shadow: 0 4px 12px rgba(222, 12, 120, 0.1);
        }
        
        .track-title {
            font-size: 16px;
            font-weight: 700;
            color: #de0c78;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .track-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }
        
        .track-detail {
            font-size: 13px;
        }
        
        .track-detail strong {
            color: #6b7280;
            font-weight: 600;
        }
        
        /* Footer */
        .modal-footer-custom {
            padding: 20px 30px;
            background: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        
        .btn {
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(222, 12, 120, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        
        .btn-secondary:hover {
            border-color: #de0c78;
            color: #de0c78;
        }
        
        /* Image Modal */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .image-modal-content {
            margin: auto;
            display: block;
            width: 90%;
            max-width: 700px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .image-modal-content img {
            width: 100%;
            height: auto;
            border-radius: 16px;
        }
        
        .close-modal {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .close-modal:hover {
            color: #de0c78;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-title {
                font-size: 20px;
            }
            
            .album-title {
                font-size: 22px;
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <!-- Header -->
        <div class="modal-header-custom">
            <div class="header-content">
                <div>
                    <div class="modal-title">
                        <i class="fas fa-music me-2"></i><?= $heading_title ?? 'Release Information'; ?>
                    </div>
                    <p style="margin-top: 8px; opacity: 0.9;">Complete details about this release</p>
                </div>
                <button class="close-btn" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="album-cover">
                <img src="<?php echo get_image('release', $res['release_banner'] ?? '', '300', '300', 'AR'); ?>" 
                     alt="<?php echo htmlspecialchars($res['release_title'] ?? ''); ?>"
                     onclick="showImageModal(this.src)"
                     onerror="this.src='<?php echo theme_url();?>images/default-album.png'">
            </div>
            <div class="album-info">
                <h1 class="album-title"><?php echo htmlspecialchars($res['release_title'] ?? 'Untitled'); ?></h1>
                <div class="artist-name">
                    <i class="fas fa-user-circle" style="color: #de0c78;"></i>
                    <?php echo htmlspecialchars($artist_name ?: 'Unknown Artist'); ?>
                </div>
                <div class="status-badge <?php echo $badge_class; ?>">
                    <i class="fas <?php echo $status_value == 1 ? 'fa-check-circle' : ($status_value == 2 ? 'fa-clock' : 'fa-times-circle'); ?>"></i>
                    <?php echo $status_text; ?>
                </div>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo $total_territories; ?></div>
                <div class="stat-label">Territories</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $total_release_stores; ?></div>
                <div class="stat-label">Stores</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo date('Y', strtotime($res['original_release_date_of_music'] ?? 'now')); ?></div>
                <div class="stat-label">Release Year</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">#<?php echo $res['release_id']; ?></div>
                <div class="stat-label">Release ID</div>
            </div>
        </div>
        
        <!-- Album Overview Section -->
        <div class="info-section">
            <div class="section-title">
                <i class="fas fa-info-circle"></i>
                <span>Album Overview</span>
            </div>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">P Line</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['p_line'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">© Line</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['c_line'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Primary Artist</div>
                    <div class="info-value"><?php echo htmlspecialchars($artist_name ?: 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Feature Artist</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['feature_artist'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">UPC/EAN</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['upc_ean'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Genre</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['genre'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Sub Genre</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['sub_genre'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Label Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($res['label_name'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Track Information Section -->
        <div class="info-section track-section">
            <div class="section-title">
                <i class="fas fa-music"></i>
                <span>Track Information</span>
            </div>
            <div class="track-item">
                <div class="track-title">
                    <i class="fas fa-headphones"></i>
                    <?php echo htmlspecialchars($res['song_name'] ?? 'Untitled Track'); ?>
                </div>
                <div class="track-details">
                    <div class="track-detail"><strong>Album Type:</strong> <?php echo htmlspecialchars($res['release_type'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>Content Type:</strong> <?php echo htmlspecialchars($res['content_type'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>ISRC:</strong> <?php echo htmlspecialchars($res['isrc'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>Song Mood:</strong> <?php echo htmlspecialchars($res['song_mood'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>CRBT Title:</strong> <?php echo htmlspecialchars($res['crbt_title'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>CRBT Cut Time:</strong> <?php echo !empty($res['time_for_crbt_cut']) ? $res['time_for_crbt_cut'] . ' secs' : 'N/A'; ?></div>
                    <div class="track-detail"><strong>Track Duration:</strong> <?php echo htmlspecialchars($res['track_duration'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>Track Language:</strong> <?php echo $lang_arr[$res['lyrics_lang']] ?? 'N/A'; ?></div>
                    <div class="track-detail"><strong>Instrumental:</strong> <?php echo ($res['is_instrumental'] > 0) ? 'Yes' : 'No'; ?></div>
                    <div class="track-detail"><strong>Lyricist & Writer:</strong> <?php echo htmlspecialchars($res['lyricist'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>Composer:</strong> <?php echo htmlspecialchars($res['composer'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>Music Director:</strong> <?php echo htmlspecialchars($res['music_director'] ?? 'N/A'); ?></div>
                    <div class="track-detail"><strong>Publisher:</strong> <?php echo htmlspecialchars($res['publisher'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Release Date Section -->
        <div class="info-section">
            <div class="section-title">
                <i class="fas fa-calendar-alt"></i>
                <span>Release Dates</span>
            </div>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Release Date</div>
                    <div class="info-value">
                        <i class="fas fa-calendar-day me-1"></i>
                        <?php echo ($res['go_live_date']) ? getDateFormat($res['go_live_date'], 1) : 'N/A'; ?>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-label">Original Music Release Date</div>
                    <div class="info-value">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo ($res['original_release_date_of_music']) ? getDateFormat($res['original_release_date_of_music'], 1) : 'N/A'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="modal-footer-custom">
            <button class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Close
            </button>
            <button class="btn btn-primary" onclick="editRelease()">
                <i class="fas fa-edit"></i> Edit Release
            </button>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <span class="close-modal" onclick="closeImageModal()">&times;</span>
        <div class="image-modal-content">
            <img id="modalImage" src="">
        </div>
    </div>
    
    <script>
        // Close modal function
        function closeModal() {
            if (window.parent && window.parent.Fancybox) {
                window.parent.Fancybox.close();
            } else {
                window.close();
            }
        }
        
        // Edit release function
        function editRelease() {
            var editUrl = "<?php echo site_url('admin/release/new_release/'.md5($res['release_id'] ?? '').'?album_type='.($res['album_type'] ?? '')); ?>";
            window.open(editUrl, '_blank');
        }
        
        // Show image modal
        function showImageModal(src) {
            var modal = document.getElementById('imageModal');
            var modalImg = document.getElementById('modalImage');
            modal.style.display = "block";
            modalImg.src = src;
        }
        
        // Close image modal
        function closeImageModal() {
            document.getElementById('imageModal').style.display = "none";
        }
        
        // Close image modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('imageModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
<?php $this->load->view("bottom_application",array('has_footer'=>false,'ws_page'=>'store_pg','is_popup'=>true));?>