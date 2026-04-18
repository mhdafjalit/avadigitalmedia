<div class="artist-details-container">
    <div class="row g-3">
        <!-- Basic Information Column -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm artist-details-table">
                        <tbody>
                            <tr>
                                <th class="w-40" style="padding: 13px;">ID : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars(isset($artist['id']) ? $artist['id'] : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th class="w-40" style="padding: 13px;">Artist Name : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars(isset($artist['name']) ? $artist['name'] : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Apple ID : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars(isset($artist['apple_id']) ? $artist['apple_id'] : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Spotify ID : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars(isset($artist['spotify_id']) ? $artist['spotify_id'] : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Meta ID : </th>
                                <td style="padding: 13px;"><?php echo !empty($artist['meta_id']) ? $artist['meta_id'] : 'N/A'; ?></td>
                            </tr>
                            
                            <tr>
                                <th style="padding: 13px;">Last Updated : </th>
                                <td style="padding: 13px;"><?php echo ($artist['last_updated'] != "0001-01-01T00:00:00Z") ? getDateFormat($artist['last_updated'],2) : "N/A"; ?></td>
                            </tr>
                            
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Details Column -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-ellipsis-h me-2"></i>Additional Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm artist-details-table">
                        <tbody>
                            <tr>
                                <th class="w-40" style="padding: 13px;">IPRS Member : </th>
                                <td style="padding: 13px;"><?php echo (isset($artist['is_iprs_member']) && $artist['is_iprs_member']) ? 'Yes' : 'No'; ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Facebook : </th>
                                <td style="padding: 13px;">
                                    <?php if (!empty($artist['facebook_artist_page_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($artist['facebook_artist_page_url']); ?>" 
                                           target="_blank" class="text-decoration-none">
                                            <i class="fab fa-facebook me-1"></i> View Profile
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Instagram : </th>
                                <td style="padding: 13px;">
                                    <?php if (!empty($artist['insta_artist_page_url'])): ?>
                                        <a href="<?php echo htmlspecialchars($artist['insta_artist_page_url']); ?>" 
                                           target="_blank" class="text-decoration-none">
                                            <i class="fab fa-instagram me-1"></i> View Profile
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Status : </th>
                                <td style="padding: 13px;">
                                    <span class="badge <?php echo (isset($artist['status']) && $artist['status'] === 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo isset($artist['status']) ? $artist['status'] : 'N/A'; ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i> Close
            </button>
            <?php /*?><button type="button" class="btn btn-primary edit-artist-btn" 
                    data-artist-id="<?php echo htmlspecialchars($artist['id']); ?>">
                <i class="fas fa-edit me-1"></i> Edit Artist
            </button><?php */?>
        </div>
    </div>
</div>

<style>
.artist-details-container {
    padding: 15px;
}
.artist-details-table th {
    font-weight: 600;
    color: #495057;
}
.artist-details-table tr {
    border-bottom: 1px solid #e9ecef;
}
.artist-details-table tr:last-child {
    border-bottom: none;
}
.card {
    border: 1px solid rgba(0,0,0,.125);
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
}
</style>