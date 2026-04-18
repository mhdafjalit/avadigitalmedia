<div class="meta-details-container">
    <div class="row g-3">
        <!-- Album Image Column -->
        <div class="col-lg-4 text-center">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Album Artwork</h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <?php if (!empty($meta['album_image'])): ?>
                        <img src="<?php echo get_image('release/songs', $meta['album_image'], 300, 300, 'AR'); ?>" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 250px;"
                             alt="Album Art">
                    <?php else: ?>
                        <div class="text-muted py-4">
                            <i class="fas fa-image fa-4x mb-2"></i>
                            <p>No Image Available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Basic Information Column -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm meta-details-table">
                        <tbody>
                            <tr>
                                <th class="w-40" style="padding: 13px;">Album ID : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars($meta['id'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Album Name : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars($meta['album_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Artist : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars($meta['artist_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Genre : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars($meta['genre'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Sub Genre : </th>
                                <td style="padding: 13px;"><?php echo htmlspecialchars($meta['sub_genre'] ?? 'N/A'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Details Column -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Release Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm meta-details-table">
                        <tbody>
                            <tr>
                                <th class="w-40" style="padding: 13px;">Release Date : </th>
                                <td style="padding: 13px;">
                                    <?php echo !empty($meta['release_date']) ? date('d M Y', strtotime($meta['release_date'])) : 'N/A'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Go Live Date : </th>
                                <td style="padding: 13px;">
                                    <?php echo !empty($meta['go_live_date']) ? date('d M Y', strtotime($meta['go_live_date'])) : 'N/A'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Created Date : </th>
                                <td style="padding: 13px;">
                                    <?php echo !empty($meta['created_date']) ? date('d M Y H:i', strtotime($meta['created_date'])) : 'N/A'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="padding: 13px;">Verify Status : </th>
                                <td style="padding: 13px;">
                                    <span class="badge <?php echo ($meta['is_verify_meta'] == '1' ? 'badge-approved' : 'badge-pending'); ?>">
										<?php echo ($meta['is_verify_meta'] == '1' ? 'Verified' : 'Un-Verified'); ?>
                                    </span>
                                </td>
                            </tr>                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metadata Section -->
    <?php if (!empty($metadata['data'])): ?>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-ellipsis-h me-2"></i>Additional Metadata</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        foreach ($metadata['data'] as $key => $value): ?>
                            <?php if (!empty($value) && !is_array($value)): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex">
                                        <strong class="me-2" style="min-width: 180px;"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</strong>
                                        <span><?php echo htmlspecialchars($value ?? ''); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    
</div>

<style>
.meta-details-container {
    padding: 15px;
}
.meta-details-table th {
    font-weight: 600;
    color: #495057;
}
.meta-details-table tr {
    border-bottom: 1px solid #e9ecef;
}
.meta-details-table tr:last-child {
    border-bottom: none;
}
.card {
    border: 1px solid rgba(0,0,0,.125);
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
}
.card-header {
    padding: 0.75rem 1.25rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0,0,0,.125);
}
</style>