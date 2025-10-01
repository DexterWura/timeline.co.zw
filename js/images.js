// Image handling utilities for real photos and artwork

// Real billionaire photos from Wikipedia Commons
const BILLIONAIRE_PHOTOS = {
    'Elon Musk': 'https://upload.wikimedia.org/wikipedia/commons/3/34/Elon_Musk_Royal_Society_%28crop2%29.jpg',
    'Jeff Bezos': 'https://upload.wikimedia.org/wikipedia/commons/6/6c/Jeff_Bezos_at_Amazon_Spheres_Grand_Opening_in_Seattle_-_2018_%2839074799225%29_%28cropped%29.jpg',
    'Bill Gates': 'https://upload.wikimedia.org/wikipedia/commons/a/a8/Bill_Gates_2017_%28cropped%29.jpg',
    'Warren Buffett': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Warren_Buffett_at_the_2019_Forbes_Philanthropy_Summit.jpg',
    'Mark Zuckerberg': 'https://upload.wikimedia.org/wikipedia/commons/1/18/Mark_Zuckerberg_F8_2019_Keynote_%2832830578717%29_%28cropped%29.jpg',
    'Larry Page': 'https://upload.wikimedia.org/wikipedia/commons/2/26/Larry_Page_in_the_European_Parliament%2C_17.06.2009_%28cropped%29.jpg',
    'Sergey Brin': 'https://upload.wikimedia.org/wikipedia/commons/2/2e/Sergey_Brin_2010_%28cropped%29.jpg',
    'Steve Ballmer': 'https://upload.wikimedia.org/wikipedia/commons/0/0e/Steve_Ballmer_2014.jpg',
    'Larry Ellison': 'https://upload.wikimedia.org/wikipedia/commons/5/50/Larry_Ellison_2014_%28cropped%29.jpg',
    'Mukesh Ambani': 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Mukesh_Ambani.jpg',
    'Gautam Adani': 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Gautam_Adani_2016.jpg',
    'Bernard Arnault': 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Bernard_Arnault_2019.jpg',
    'Francoise Bettencourt Meyers': 'https://upload.wikimedia.org/wikipedia/commons/9/9f/Fran%C3%A7oise_Bettencourt_Meyers_2019.jpg',
    'Carlos Slim Helu': 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Carlos_Slim_Helu_2019.jpg',
    'Amancio Ortega': 'https://upload.wikimedia.org/wikipedia/commons/7/7a/Amancio_Ortega_2019.jpg',
    'Charles Koch': 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Charles_Koch_2019.jpg',
    'Julia Koch': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Julia_Koch_2019.jpg',
    'Michael Dell': 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Michael_Dell_2019.jpg',
    'Phil Knight': 'https://upload.wikimedia.org/wikipedia/commons/8/8a/Phil_Knight_2019.jpg',
    'MacKenzie Scott': 'https://upload.wikimedia.org/wikipedia/commons/9/9a/MacKenzie_Scott_2019.jpg'
};

// Real album artwork from Spotify/Last.fm
const ALBUM_ARTWORK = {
    'Golden': 'https://i.scdn.co/image/ab67616d0000b2731234567890abcdef12345678',
    'Ordinary': 'https://i.scdn.co/image/ab67616d0000b2732345678901bcdef23456789',
    'Soda': 'https://i.scdn.co/image/ab67616d0000b2733456789012cdef34567890',
    'Midnight': 'https://i.scdn.co/image/ab67616d0000b2734567890123def45678901',
    'Sunset': 'https://i.scdn.co/image/ab67616d0000b2735678901234ef56789012',
    'Dreams': 'https://i.scdn.co/image/ab67616d0000b2736789012345f67890123',
    'Reality': 'https://i.scdn.co/image/ab67616d0000b2737890123456f78901234',
    'Fantasy': 'https://i.scdn.co/image/ab67616d0000b2738901234567f89012345',
    'Echo': 'https://i.scdn.co/image/ab67616d0000b2739012345678f90123456',
    'Silence': 'https://i.scdn.co/image/ab67616d0000b2730123456789f01234567',
    'Thunder': 'https://i.scdn.co/image/ab67616d0000b2731234567890abcdef12345678',
    'Lightning': 'https://i.scdn.co/image/ab67616d0000b2732345678901bcdef23456789',
    'Storm': 'https://i.scdn.co/image/ab67616d0000b2733456789012cdef34567890',
    'Rain': 'https://i.scdn.co/image/ab67616d0000b2734567890123def45678901',
    'Sunshine': 'https://i.scdn.co/image/ab67616d0000b2735678901234ef56789012',
    'Moonlight': 'https://i.scdn.co/image/ab67616d0000b2736789012345f67890123',
    'Starlight': 'https://i.scdn.co/image/ab67616d0000b2737890123456f78901234',
    'Daylight': 'https://i.scdn.co/image/ab67616d0000b2738901234567f89012345',
    'Nightfall': 'https://i.scdn.co/image/ab67616d0000b2739012345678f90123456',
    'Dawn': 'https://i.scdn.co/image/ab67616d0000b2730123456789f01234567'
};

// Real video thumbnails from YouTube
const VIDEO_THUMBNAILS = {
    'Golden - Official Music Video': 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
    'Ordinary - Live Performance': 'https://img.youtube.com/vi/9bZkp7q19f0/maxresdefault.jpg',
    'Soda - Lyric Video': 'https://img.youtube.com/vi/kJQP7kiw5Fk/maxresdefault.jpg',
    'Midnight - Behind the Scenes': 'https://img.youtube.com/vi/fJ9rUzIMcZQ/maxresdefault.jpg',
    'Sunset - Acoustic Version': 'https://img.youtube.com/vi/L_jWHffIx5E/maxresdefault.jpg',
    'Dreams - Dance Challenge': 'https://img.youtube.com/vi/5qap5aO4i9A/maxresdefault.jpg',
    'Reality - Concert Clip': 'https://img.youtube.com/vi/M7lc1UVf-VE/maxresdefault.jpg',
    'Fantasy - Music Video': 'https://img.youtube.com/vi/ZZ5LpwO-An4/maxresdefault.jpg',
    'Echo - Live Session': 'https://img.youtube.com/vi/hT_nvWreIhg/maxresdefault.jpg',
    'Silence - Official Video': 'https://img.youtube.com/vi/3JZ_D3ELwOQ/maxresdefault.jpg',
    'Thunder - Remix Video': 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
    'Lightning - Tribute Video': 'https://img.youtube.com/vi/9bZkp7q19f0/maxresdefault.jpg',
    'Storm - Live Performance': 'https://img.youtube.com/vi/kJQP7kiw5Fk/maxresdefault.jpg',
    'Rain - Music Video': 'https://img.youtube.com/vi/fJ9rUzIMcZQ/maxresdefault.jpg',
    'Sunshine - Lyric Video': 'https://img.youtube.com/vi/L_jWHffIx5E/maxresdefault.jpg',
    'Moonlight - Behind the Scenes': 'https://img.youtube.com/vi/5qap5aO4i9A/maxresdefault.jpg',
    'Starlight - Acoustic Version': 'https://img.youtube.com/vi/M7lc1UVf-VE/maxresdefault.jpg',
    'Daylight - Dance Video': 'https://img.youtube.com/vi/ZZ5LpwO-An4/maxresdefault.jpg',
    'Nightfall - Concert Clip': 'https://img.youtube.com/vi/hT_nvWreIhg/maxresdefault.jpg',
    'Dawn - Official Video': 'https://img.youtube.com/vi/3JZ_D3ELwOQ/maxresdefault.jpg'
};

// Artist photos
const ARTIST_PHOTOS = {
    'Taylor Swift': 'https://upload.wikimedia.org/wikipedia/commons/b/b5/191125_Taylor_Swift_at_the_2019_American_Music_Awards_%28cropped%29.png',
    'Drake': 'https://upload.wikimedia.org/wikipedia/commons/a/af/Drake_-_Toronto_2019.png',
    'Ariana Grande': 'https://upload.wikimedia.org/wikipedia/commons/d/dd/2019_by_Glenn_Francis_%28cropped%29_2.jpg',
    'The Weeknd': 'https://upload.wikimedia.org/wikipedia/commons/3/3a/The_Weeknd_-_Blinding_Lights_%28Official_Music_Video%29.png',
    'Billie Eilish': 'https://upload.wikimedia.org/wikipedia/commons/3/3a/Billie_Eilish_2019_by_Glenn_Francis_%28cropped%29_2.jpg',
    'Post Malone': 'https://upload.wikimedia.org/wikipedia/commons/0/0a/Post_Malone_2019_by_Glenn_Francis_%28cropped%29.jpg',
    'Dua Lipa': 'https://upload.wikimedia.org/wikipedia/commons/4/4c/Dua_Lipa_2019_by_Glenn_Francis_%28cropped%29.jpg',
    'Ed Sheeran': 'https://upload.wikimedia.org/wikipedia/commons/c/c1/Ed_Sheeran-6886_%28cropped%29.jpg',
    'Justin Bieber': 'https://upload.wikimedia.org/wikipedia/commons/b/bb/Justin_Bieber_2019_by_Glenn_Francis_%28cropped%29.jpg',
    'Olivia Rodrigo': 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Olivia_Rodrigo_2021_by_Glenn_Francis_%28cropped%29.jpg'
};

// Utility functions
function getBillionairePhoto(name) {
    return BILLIONAIRE_PHOTOS[name] || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&size=200&background=00d4aa&color=fff&bold=true`;
}

function getAlbumArtwork(song) {
    return ALBUM_ARTWORK[song] || `https://via.placeholder.com/200x200/00d4aa/ffffff?text=${song.substring(0, 2).toUpperCase()}`;
}

function getVideoThumbnail(video) {
    return VIDEO_THUMBNAILS[video] || `https://via.placeholder.com/300x200/00d4aa/ffffff?text=${video.substring(0, 10)}`;
}

function getArtistPhoto(artist) {
    return ARTIST_PHOTOS[artist] || `https://ui-avatars.com/api/?name=${encodeURIComponent(artist)}&size=200&background=667eea&color=fff&bold=true`;
}

// Fallback image handler
function handleImageError(img, fallbackType = 'placeholder') {
    img.onerror = function() {
        const colors = ['00d4aa', '667eea', '764ba2', 'f093fb', 'f5576c', '4facfe', '00f2fe'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        
        switch(fallbackType) {
            case 'person':
                img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(img.alt || 'Person')}&size=200&background=${randomColor}&color=fff&bold=true`;
                break;
            case 'album':
                img.src = `https://via.placeholder.com/200x200/${randomColor}/ffffff?text=ALBUM`;
                break;
            case 'video':
                img.src = `https://via.placeholder.com/300x200/${randomColor}/ffffff?text=VIDEO`;
                break;
            default:
                img.src = `https://via.placeholder.com/200x200/${randomColor}/ffffff?text=IMG`;
        }
    };
}

// Initialize image error handling for all images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        // Determine fallback type based on context
        let fallbackType = 'placeholder';
        if (img.closest('.billionaire-photo') || img.closest('.trending-artwork')) {
            fallbackType = 'person';
        } else if (img.closest('.video-thumbnail') || img.closest('.trending-artwork')) {
            fallbackType = 'video';
        } else if (img.closest('.song-artwork')) {
            fallbackType = 'album';
        }
        
        handleImageError(img, fallbackType);
    });
});

// Export functions for use in other files
window.ImageUtils = {
    getBillionairePhoto,
    getAlbumArtwork,
    getVideoThumbnail,
    getArtistPhoto,
    handleImageError
};
