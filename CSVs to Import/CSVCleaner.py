import csv

def remove_non_delimiter_commas(input_file, output_file):
    try:
        with open(input_file, mode='r', newline='', encoding='utf-8') as infile:
            reader = csv.reader(infile)
            cleaned_rows = []
            
            for row in reader:
                # Remove commas from each field
                cleaned_row = [field.replace(',', '') for field in row]
                cleaned_rows.append(cleaned_row)
    except UnicodeDecodeError:
        print(f"UnicodeDecodeError encountered in {input_file}. Retrying with 'latin-1' encoding.")
        with open(input_file, mode='r', newline='', encoding='latin-1') as infile:
            reader = csv.reader(infile)
            cleaned_rows = []
            
            for row in reader:
                # Remove commas from each field
                cleaned_row = [field.replace(',', '') for field in row]
                cleaned_rows.append(cleaned_row)

    with open(output_file, mode='w', newline='', encoding='utf-8') as outfile:
        writer = csv.writer(outfile)
        writer.writerows(cleaned_rows)

# List of files to process
files_to_clean = [
    ('Bills.csv', 'Bills_cleaned.csv'),
    ('Candidates.csv', 'Candidates_cleaned.csv'),
    ('Votes.csv', 'Votes_cleaned.csv')
]

for input_file, output_file in files_to_clean:
    remove_non_delimiter_commas(input_file, output_file)
    print(f"Processed {input_file} -> {output_file}")