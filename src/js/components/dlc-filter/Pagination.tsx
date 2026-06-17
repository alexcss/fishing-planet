import React from 'react'

interface PaginationProps {
  currentPage: number
  totalPages: number
  onPageChange: (page: number) => void
}

const Pagination: React.FC<PaginationProps> = ({ currentPage, totalPages, onPageChange }) => {
  if (totalPages <= 1) return null

  const getPageNumbers = () => {
    const pages: (number | string)[] = []

    if (totalPages <= 5) {
      for (let i = 1; i <= totalPages; i++) {
        pages.push(i)
      }
    } else {
      if (currentPage <= 3) {
        pages.push(1, 2, 3, '...', totalPages)
      } else if (currentPage >= totalPages - 2) {
        pages.push(1, '...', totalPages - 2, totalPages - 1, totalPages)
      } else {
        pages.push(1, '...', currentPage, '...', totalPages)
      }
    }

    return pages
  }

  return (
    <div className="flex items-center justify-center gap-8 pt-40">
      {/* Previous Button */}
      <button
        onClick={() => onPageChange(currentPage - 1)}
        disabled={currentPage === 1}
        className="fp-btn-sqr fp-btn-sqr-light"
        aria-label="Previous page"
      >
        <svg className="h-24 w-24">
          <use href="#icon-arrow-l" />
        </svg>
      </button>

      {/* Page Numbers */}
      <div className="flex items-center gap-8">
        {getPageNumbers().map((page, index) => (
          <React.Fragment key={index}>
            {page === '...' ? (
              <span className="font-heading text-24 px-8 text-white/50">...</span>
            ) : (
              <button
                onClick={() => onPageChange(page as number)}
                className={`font-heading text-18 fp-btn-sqr fp-btn-sqr-light flex ${currentPage === page ? 'active' : ''}`}
              >
                {page}
              </button>
            )}
          </React.Fragment>
        ))}
      </div>

      {/* Next Button */}
      <button
        onClick={() => onPageChange(currentPage + 1)}
        disabled={currentPage === totalPages}
        className="fp-btn-sqr fp-btn-sqr-light"
        aria-label="Next page"
      >
        <svg className="h-24 w-24">
          <use href="#icon-arrow-r" />
        </svg>
      </button>
    </div>
  )
}

export default Pagination
