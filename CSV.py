import pandas as pd

# Load the CSV
input_file = 'VotesSetup.csv'  # Replace with your actual filename
df = pd.read_csv(input_file, dtype=str)

# Assume the first column is CandidateID
candidate_col = df.columns[0]
bill_cols = df.columns[1:]

# Assign numeric BillIDs starting from 1
bill_id_map = {bill: idx + 1 for idx, bill in enumerate(bill_cols)}

# Melt the DataFrame to long format
melted = df.melt(id_vars=[candidate_col], value_vars=bill_cols,
                 var_name='BillName', value_name='Vote')

# Map BillNames to BillIDs
melted['BillID'] = melted['BillName'].map(bill_id_map)

# Reorder and rename columns
output_df = melted[[candidate_col, 'BillID', 'Vote']]
output_df.columns = ['CandidateID', 'BillID', 'Vote']

# Save to CSV
output_df.to_csv('transformed_votes.csv', index=False)

print("Done! Output saved as 'transformed_votes.csv'")